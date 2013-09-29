<?php

class Achievement {

    private $_user_id;
    
    public function setUserId($user_id) {
        $this->_user_id = $user_id;
    }

    public function completeAchievements($user_id) {
        $this->_user_id = $user_id;
        
        $this->tracksAchievement();
        $this->sameTrackAchievement();
        $this->artistAchievement();
        $this->albumAchievement();
    }
    
    public function completeAchievement($achievement_id) {
        global $db;
        
        $result = $db->select('user_achievement', null, "WHERE `user_id`='" . $this->_user_id . "' AND `achievement_id`='" . $achievement_id . "'");
        
        if ($result['error']) {
            $user_achievement = array(
                'user_id' => $this->_user_id,
                'achievement_id' => $achievement_id,
                'date' => date('Y-m-d H:i:s')
            );

            $db->insert('user_achievement', $user_achievement);
        }
    }

    private function tracksAchievement() {
        global $db;
        
        $result = $db->query("SELECT SUM(playcount) AS `total` FROM `user_track` WHERE `user_id`='" . $this->_user_id . "'");
        if (!$result['error']) {
            $total = $result['data'][0]->total;
            
            if ($total >= 1) {
                $this->completeAchievement(1);
            }
            if ($total >= 10) {
                $this->completeAchievement(2);
            }
            if ($total >= 100) {
                $this->completeAchievement(3);
            }
            if ($total >= 250) {
                $this->completeAchievement(4);
            }
            if ($total >= 500) {
                $this->completeAchievement(5);
            }
            if ($total >= 1000) {
                $this->completeAchievement(6);
            }
        }
    }

    private function sameTrackAchievement() {
        global $db;
        
        $result = $db->firstCell('user_track', 'playcount', "WHERE `user_id`='" . $this->_user_id . "' ORDER BY `playcount` DESC");
        if (!$result['error']) {
            $total = $result['data'];
            
            if ($total >= 3) {
                $this->completeAchievement(7);
            }
            if ($total >= 5) {
                $this->completeAchievement(8);
            }
            if ($total >= 10) {
                $this->completeAchievement(9);
            }
            if ($total >= 15) {
                $this->completeAchievement(10);
            }
            if ($total >= 20) {
                $this->completeAchievement(11);
            }
            if ($total >= 25) {
                $this->completeAchievement(12);
            }
        }
    }

    private function artistAchievement() {
        global $db;
        
        $result = $db->query("SELECT `a`.`name`, COUNT(`a`.`name`) AS `total` FROM `user_track` AS `ut` LEFT JOIN `track` AS `t` ON `ut`.`track_id`=`t`.`id` LEFT JOIN `artist` AS `a` ON `t`.`artist_id`=`a`.`id` WHERE `ut`.`user_id`='" . $this->_user_id . "' GROUP BY `a`.`name` ORDER BY `total` DESC LIMIT 0,1");
        if (!$result['error']) {
            $total = $result['data'][0]->total;
            
            if ($total >= 3) {
                $this->completeAchievement(13);
            }
            if ($total >= 5) {
                $this->completeAchievement(14);
            }
            if ($total >= 10) {
                $this->completeAchievement(15);
            }
            if ($total >= 15) {
                $this->completeAchievement(16);
            }
            if ($total >= 20) {
                $this->completeAchievement(17);
            }
            if ($total >= 25) {
                $this->completeAchievement(18);
            }
        }
    }

    private function albumAchievement() {
        global $db;
        
        $result = $db->query("SELECT `a`.`name`, COUNT(`a`.`name`) AS `total` FROM `user_track` AS `ut` LEFT JOIN `track` AS `t` ON `ut`.`track_id`=`t`.`id` LEFT JOIN `album` AS `a` ON `t`.`album_id`=`a`.`id` WHERE `ut`.`user_id`='" . $this->_user_id . "' GROUP BY `a`.`name` ORDER BY `total` DESC LIMIT 0,1");
        if (!$result['error']) {
            $total = $result['data'][0]->total;
            
            if ($total >= 3) {
                $this->completeAchievement(19);
            }
            if ($total >= 5) {
                $this->completeAchievement(20);
            }
            if ($total >= 10) {
                $this->completeAchievement(21);
            }
            if ($total >= 15) {
                $this->completeAchievement(22);
            }
            if ($total >= 20) {
                $this->completeAchievement(23);
            }
            if ($total >= 25) {
                $this->completeAchievement(24);
            }
        }
    }
    
}

?>