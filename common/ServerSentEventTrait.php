<?php
/**
 * Created by PhpStorm.
 * User: KNakamoto
 * Date: 2016/03/08
 * Time: 20:51
 */

namespace app\common;

/**
 * SSEを扱うためのトレイト
 * ここにイベントとメッセージを送る設定メソッドを書いていきます。要件に合わせて内容を調整してください
 */

trait ServerSentEventTrait
{
    /**
     * ServerSendEventのフォーマットで、デフォルトメッセージを送信する
     * @param string $id メッセージID
     * @param string $message メッセージの内容
     */
    public function sendMessage($id, $message)
    {
        $this->_sendMessage($id, $message);
    }

    /**
     * ServerSendEventのフォーマットで、errorメッセージを送信する
     * @param string $id メッセージID
     * @param string $message メッセージの内容
     */
    public function sendError($id, $message)
    {
        $this->_sendMessage($id, $message, 'error');
    }

    /**
     * ServerSendEventのフォーマットで、tooManyErrorメッセージを送信する
     * @param string $id メッセージID
     * @param string $message メッセージの内容
     */
    public function sendTooManyError($id, $message)
    {
        $this->_sendMessage($id, $message, 'tooManyError');
    }

    /**
     * ServerSendEventのフォーマットで、completeメッセージを送信する
     * @param string $id メッセージID
     * @param string $message メッセージの内容
     */
    public function sendComplete($id, $message)
    {
        $this->_sendMessage($id, $message, 'complete');
    }

    /**
     * ServerSendEventのフォーマットで、loadingErrorメッセージを送信する
     * @param string $id メッセージID
     * @param string $message メッセージの内容
     */
    public function sendLoadingError($id, $message)
    {
        $this->_sendMessage($id, $message, 'loadingError');
    }

    /**
     * ServerSendEventのフォーマットで、emptyErrorメッセージを送信する
     * @param string $id メッセージID
     * @param string $message メッセージの内容
     */
    public function sendEmptyError($id, $message)
    {
        $this->_sendMessage($id, $message, 'emptyError');
    }

    /**
     * ServerSendEventのフォーマットで、exceptionErrorメッセージを送信する
     * @param string $id メッセージID
     * @param string $message メッセージの内容
     */
    public function sendExceptionError($id, $message)
    {
        $this->_sendMessage($id, $message, 'exceptionError');
    }

    /**
     * @param $id
     * @param $message
     * @param null $event
     */
    protected function _sendMessage($id, $message, $event = null)
    {
        echo "id: $id" . PHP_EOL;
        if (isset($event)) echo "event: $event" . PHP_EOL;
        echo "data: $message" . PHP_EOL;
        echo PHP_EOL;
        ob_flush();
        flush();
    }

    /**
     * ServerSendEventを扱うためのHttpヘッダーを書き出す
     */
    protected function renderHeader()
    {
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        header("Access-Control-Allow-Origin: *");
    }

    /**
     * ServerSendEventによる通信が途切れた際の、再接続時間を設定する
     * @param integer $sec 再接続を行うまでの秒数
     */
    protected function setRetry($sec)
    {
        echo "retry: {$sec}" . PHP_EOL;
    }
}
