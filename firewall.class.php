<?php

class Firewall
{
        private static $ip = null;
        private static $limit = 0;

        private static $whiteListTable = "whitelist";
        private static $blackListTable = "blacklist";
        private static $temporaryListTable = "temporarylist";
        private static $minuteListTable = "minutelist";

        // retrieves user's IP address
        public static function getIP()
        {
                return isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
        }

        // set tables
        public static function setTable($which, $table)
        {
                switch ($which) {
                        case "whitelist":
                                self::$whiteListTable = $table;
                                break;
                        case "blacklist":
                                self::$blackListTable = $table;
                                break;
                        case "temporarylist":
                                self::$temporaryListTable = $table;
                                break;
                        case "minutelist":
                                self::$minuteListTable = $table;
                                break;
                }
                return new static();
        }

        // returns date now 
        private static function dateNow($timestamp = null)
        {
                return $timestamp == null ? date("Y-m-d H:i") : date("Y-m-d H:i", $timestamp);
        }

        // check if ip is in whitelist
        private static function isBlackListed()
        {
                return !(db::empty("SELECT ip FROM " . self::$blackListTable . " WHERE ip='" . self::$ip . "';"));
        }

        // check if ip is temporary blocked
        private static function isTemporaryBlocked()
        {
                return !(db::empty("SELECT ip FROM " . self::$temporaryListTable . " WHERE ip='" . self::$ip . "' AND blocked='s'"));
        }

        // check if ip is in whitelist
        private static function isWhiteListed()
        {
                return !(db::empty("SELECT ip FROM " . self::$whiteListTable . " WHERE ip='" . self::$ip . "';"));
        }

        // update minute list
        private static function updateMinuteList()
        {
                $now = self::dateNow();
                $minute = self::dateNow((strtotime($now . " - 1 minutes")));

                db::execute("DELETE FROM " . self::$minuteListTable . " WHERE created_at<='" . $minute . "'");
                $query = db::query("SELECT * FROM " . self::$minuteListTable . " WHERE ip='" . self::$ip . "'");
                if (db::empty($query)) {
                        db::insert([
                                "ip" => self::$ip,
                                "created_at" => $now,
                                "requests" => 1
                        ], self::$minuteListTable);
                } else {
                        db::execute("UPDATE " . self::$minuteListTable . " SET requests = requests+1 WHERE ip='" . self::$ip . "'");
                }
        }

        // update temporary list
        private static function updateTemporaryList()
        {
                $now = self::dateNow();
                $day = self::dateNow((strtotime($now . " - 1 days")));

                db::execute("UPDATE " . self::$temporaryListTable . " SET blocked='n' WHERE n_times_blocked <=3 AND created_at<='$day';");
                $queryMinute = db::query("SELECT ip FROM " . self::$minuteListTable . " WHERE requests>" . self::$limit);
                if (!db::empty($queryMinute)) {
                        while ($blocked = db::fetch($queryMinute)) {
                                $queryTemporary = db::query("SELECT * FROM " . self::$temporaryListTable . " WHERE ip ='" . $blocked['ip'] . "'");
                                if (db::empty($queryTemporary)) {
                                        db::insert([
                                                "ip" => $blocked['ip'],
                                                "blocked" => "s",
                                                "n_times_blocked" => 1,
                                                "created_at" => self::dateNow()
                                        ], self::$temporaryListTable);
                                } else {
                                        db::execute("UPDATE " . self::$temporaryListTable . " SET n_times_blocked = n_times_blocked+1, blocked = 's' WHERE ip='" . $blocked['ip'] . "'");
                                }
                        }
                        db::execute("DELETE FROM " . self::$minuteListTable . " WHERE requests>" . self::$limit);
                }
        }

        // update black list
        private static function updateBlackList()
        {
                $query = db::query("SELECT * FROM " . self::$temporaryListTable . " WHERE n_times_blocked>3");
                if (!db::empty($query)) {
                        while ($data = db::fetch($query)) {
                                db::insert([
                                        "ip" => self::$ip,
                                        "created_at" => self::dateNow()
                                ], self::$blackListTable);
                        }
                        db::execute("DELETE FROM " . self::$temporaryListTable . " WHERE n_times_blocked>" . self::$limit);
                }
        }

        // start firewall
        public static function init($limit = 20, $ip = null)
        {
                self::$ip = ($ip == null ? self::getIP() : $ip);
                self::$limit = $limit;

                if (!self::isWhiteListed()) {
                        self::updateMinuteList();
                        self::updateTemporaryList();
                        self::updateBlackList();
                }
                if (self::isBlackListed()) {
                        header("HTTP/1.1 401 Forbidden");
                        exit;
                }
                if (self::isTemporaryBlocked()) {
                        header("HTTP/1.1 429 Too Manny Requests");
                        exit;
                }
        }
}