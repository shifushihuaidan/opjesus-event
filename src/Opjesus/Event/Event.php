<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/29
 * Time: 10:08
 */

namespace Opjesus\Event;


class Event
{
    public $name;

    public $sender;

    public $handled = false;

    public $data;

    protected static $_events = [];

    /**
     * 绑定事件
     *
     * @param string $name 事件名称
     * @param callable $handler 事件处理程序
     * @param array $data 事件处理程序的参数（数组）
     * @param bool $append 是否新增在已存在的事件的尾部(一个时间可以有多个事件处理程序)
     */
    public static function on($name, $handler, $data = [], $append = true)
    {
        if ($append || (!isset(self::$_events[$name]) && empty(self::$_events[$name]))) {
            self::$_events[$name][] = [$handler, $data];
        } else {
            array_unshift(self::$_events[$name], [$handler, $data]);
        }
    }

    /**
     * 解除事件
     *
     * @param string $name 解除的事件名称
     * @param callable $handler 解除的事件处理程序，为null时解除改时间所有事件处理程序
     * @return bool 返回true或者false
     */
    public static function off($name, $handler = null)
    {
        if (!isset(self::$_events[$name]) || empty(self::$_events[$name])) {
            return false;
        }
        if (null === $handler) {
            unset(self::$_events[$name]);
            return true;
        }

        $removed = false;
        if (isset(self::$_events[$name])) {

            foreach (self::$_events[$name] as $i => $event) {
                if ($handler === $event[0]) {
                    unset(self::$_events[$name][$i]);
                    $removed = true;
                }
            }
            if ($removed) {
                self::$_events[$name] = array_values(self::$_events[$name]);
                return $removed;
            }
        }

        return $removed;
    }

    /**
     * 解除所有绑定事件
     *
     */
    public static function offAll()
    {
        self::$_events = [];
    }

    /**
     * 触发事件
     *
     * @param string $name 触发的事件名称
     * @param Event|null $event 触发的事件的相关数据(一般不填就行，以后扩展需要)
     */
    public function trigger($name, Event $event = null)
    {
        if(isset(self::$_events[$name]) && !empty(self::$_events[$name])) {
            if(null === $event) {
                $event = new static();
            }
            if(null === $event->sender) {
                 $event->sender = $this;
            }

            $event->handled = false;
            $event->name    = $name;

            foreach (self::$_events[$name] as $handler) {
                $event->data = $handler[1];
                call_user_func_array($handler[0], $event->data);

                if($event->handled) {
                    return;
                }
            }
        }
    }

    /**
     * 查询某一事件是否已注册
     *
     * @param string $name 事件名称
     * @return bool true或者false
     */
    public static function hasHandlers($name)
    {
        if(isset(self::$_events[$name])) {
            return true;
        }

        return false;
    }
}