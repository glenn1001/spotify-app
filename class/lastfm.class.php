<?php

class LastFM {

    private $_user_id;
    private $_username;
    private $_apiKey = 'd56b5e3ff69b7508741455785d98f11b';
    private $_perPage = 2500;
    private $_unparsedTracks = array();
    private $_attr;
    private $_artists = array();
    private $_albums = array();
    private $_tracks = array();

    public function getTracks($user_id, $username, $page = 1) {
        $this->_user_id = $user_id;
        $this->_username = $username;

        $url = 'http://ws.audioscrobbler.com/2.0/?method=library.gettracks&api_key=' . $this->_apiKey . '&user=' . $this->_username . '&format=json&limit=' . $this->_perPage . '&page=' . $page;
        $options = array(
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 120
        );
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);

        if (!isset($result->error)) {
            if (is_array($result->tracks->track)) {
                $this->_unparsedTracks = array_merge($this->_unparsedTracks, $result->tracks->track);

                $this->_attr = $result->tracks->{'@attr'};
                if ($this->_attr->totalPages > $page) {
//                    $this->getTracks($this->_user_id, $this->_username, $page + 1);
                }

                $this->parseTracks();

                return array('error' => false, 'response' => 'Your tracks and achievements have been updated!');
            } else {
                return array('error' => true, 'response' => 'Something went wrong, please try again later!');
            }
        } else {
            return array('error' => true, 'errorCode' => $result->error, 'response' => $result->message);
        }
    }

    private function parseTracks() {
        foreach ($this->_unparsedTracks as $track) {
            $track->artist->mbid = ($track->artist->mbid == '' ? $track->artist->name : $track->artist->mbid);
            $track->mbid = ($track->mbid == '' ? $track->name : $track->mbid);

            $this->_artists[$track->artist->mbid] = (object) array(
                        'name' => $track->artist->name,
                        'lastfm_id' => $track->artist->mbid,
                        'url' => $track->artist->url
            );

            $this->_albums[$track->album->name] = (object) array(
                        'name' => $track->album->name
            );

            $this->_tracks[$track->mbid] = (object) array(
                        'name' => $track->name,
                        'duration' => $track->duration,
                        'playcount' => $track->playcount,
                        'tagcount' => $track->tagcount,
                        'lastfm_id' => $track->mbid,
                        'url' => $track->url,
                        'artist_id' => $track->artist->mbid,
                        'album_id' => $track->album->name
            );
        }

        $this->addData();
    }

    private function addData() {
        $this->addArtists();
        $this->addAlbums();
        $this->addTracks();
    }

    private function addArtists() {
        global $db;

        foreach ($this->_artists as $artist) {
            $result = $db->firstCell('artist', 'id', "WHERE `lastfm_id`='" . $artist->lastfm_id . "'");

            if ($result['error']) {
                $db->insert('artist', $artist);
            }
        }
    }

    private function addAlbums() {
        global $db;

        foreach ($this->_albums as $album) {
            $result = $db->firstCell('album', 'id', "WHERE `lastfm_id`='" . $album->name . "'");

            if ($result['error']) {
                $db->insert('album', $album);
            }
        }
    }

    private function addTracks() {
        global $db, $achievement;

        foreach ($this->_tracks as $track) {
            $result = $db->firstCell('track', 'id', "WHERE `lastfm_id`='" . $track->lastfm_id . "'");

            if ($result['error']) {
                $artist_id = $db->firstCell('artist', 'id', "WHERE `lastfm_id`='" . $track->artist_id . "'");
                $track->artist_id = $artist_id['data'];

                $album_id = $db->firstCell('album', 'id', "WHERE `name`='" . $track->album_id . "'");
                $track->album_id = $album_id['data'];

                $tmpTrack = array(
                    'name' => $track->name,
                    'duration' => $track->duration,
                    'lastfm_id' => $track->lastfm_id,
                    'url' => $track->url,
                    'artist_id' => $track->artist_id,
                    'album_id' => $track->album_id
                );

                $db->insert('track', $tmpTrack);
            }

            $result = $db->firstCell('track', 'id', "WHERE `lastfm_id`='" . $track->lastfm_id . "'");
            $track->id = $result['data'];
            $this->addTrackToUser($track);
        }

        $achievement->completeAchievements($this->_user_id);
    }

    private function addTrackToUser($track) {
        global $db;

        $user_track = array(
            'user_id' => $this->_user_id,
            'track_id' => $track->id,
            'playcount' => $track->playcount,
            'tagcount' => $track->tagcount
        );

        $result = $db->firstCell('user_track', 'id', "WHERE `user_id`='" . $this->_user_id . "' AND `track_id`='" . $track->id . "'");
        if ($result['error']) {
            $db->insert('user_track', $user_track);
        } else {
            $db->update('user_track', $user_track, "WHERE `id`='" . $result['data'] . "'");
        }
    }

}

?>