<?php

namespace App\models;

class Messages
{
    public static $types = ["info", "warn", "error", "success"];

    /**
     * Add message to be rendered
     * @param $message
     * @param string $type
     */
    public static function addMessage($message, $type = "info")
    {
        $type = in_array($type, self::$types) ? $type : "info";

        if (strlen($message) > 0) {
            $_SESSION["messages"] = $_SESSION["messages"] ?? [];
            $_SESSION["messages"][] = [$message, $type];
        }
    }

    /**
     * Get all messages
     * @return array|mixed
     */
    public static function getMessages()
    {
        if (isset($_SESSION["messages"])) {
            $messages = $_SESSION["messages"];
            return $messages;
        } else {
            return [];
        }
    }

    public static function clear()
    {
        unset($_SESSION["messages"]);
    }
}
