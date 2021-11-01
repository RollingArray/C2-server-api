<?php
    namespace C2;
    
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Processor\UidProcessor;

    
    abstract class BaseAPI
    {
        /**
        * Property: method
        * The HTTP method this request was made in, either GET, POST, PUT or DELETE
        */
        protected $method = '';
        /**
        * Property: endpoint
        * The Model requested in the URI. eg: /files
        */
        protected $endpoint = '';
        /**
        * Property: verb
        * An optional additional descriptor about the endpoint, used for things that can
        * not be handled by the basic methods. eg: /files/process
        */
        protected $verb = '';
        /**
        * Property: args
        * Any additional URI components after the endpoint and verb have been removed, in our
        * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
        * or /<endpoint>/<arg0>
        */
        protected $args = Array();
        /**
        * Property: file
        * Stores the input of the PUT request
        */
        protected $file = Null;

        protected $logger = Null;

        protected $settings = Null;

        /**
        * Constructor: __construct
        * Allow for CORS, assemble and pre-process the data
        */
        public function __construct($settings) {
            // A couple of test log messages
            //echo 'The class "', __CLASS__, '" was initiated!<br />';
            
            $this->settings = $settings;
            $this->logger();
        }

        private function logger()
        {
            $this->logger = new Logger($this->settings['logger']['name']);
            $this->logger->pushProcessor(new UidProcessor());
            // Add log file handler
            $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../'.$this->settings['logger']['path'], Logger::INFO));
        }

        public function logEvents()
        {
            return $this->logger;
        }

        public function getPostData()
        {
            return json_decode(file_get_contents("php://input"));
        }

        public function sanitizeInput($input)
        {
            return $input;
        }

        public function getAuthorizationSessionObject()
        {
            $headers = apache_request_headers();
            return $headers['Auth'];
        }

        function __destruct() {
            //echo 'The class "', __CLASS__, '" was destroyed.<br />';
        }
    }
?>