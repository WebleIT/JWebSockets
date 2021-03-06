<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;



// Load FOF
include_once JPATH_LIBRARIES.'/fof/include.php';
include_once dirname(__FILE__) . '/pusher.php';

class WsApp implements MessageComponentInterface {

    private static $application = null;
    private static $loop = null;

    private static $pusher = null;

    private static $connections = array();
    private static $activeConn = null;

	public function __construct($app = null, $loop = null) {
		static $wsdispatcher;
        
        self::$application = $app;
        self::$loop = $loop;

        $app_id = ''; // your pusher app id
        $app_key = ''; // your pusher app key
        $app_secret = ''; // your pusher app secret

        self::$pusher = new Pusher( $app_key, $app_secret, $app_id );
	}

    public function onOpen(ConnectionInterface $conn) {
        self::$connections[] = $conn;
        $conn->Session->start();
    }

    public static function getConnections() {
        return self::$connections;
    }


    public static function getPusher() {
        return self::$pusher;
    }

    public static function getActiveConnection() {
        return self::$activeConn;
    }

    public static function getApplication() {
        return self::$application;
    }

    public static function getLoop() {
        return self::$loop;
    }


    public function onMessage(ConnectionInterface $from, $msg) {
    	$input = new FOFInput(json_decode($msg));
        self::$activeConn = $from;

        try {
            FOFDispatcher::getTmpInstance(null, null, array('input' => $input))->dispatch();
    	} catch(Exception $e) {
            echo $e;
    	}

        self::$activeConn = null;
    }

    public function onClose(ConnectionInterface $conn) {
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
    }
}