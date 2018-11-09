<?php 

    /**
     * StremLogger
     * 
     * Minimalist and fast PSR-3 Stream logger.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-logger
     * @see    https://github.com/php-fig/log/blob/master/Psr/Log
     */

    namespace Embryo\Log;

    use Psr\Http\Message\StreamFactoryInterface;
    use Psr\Log\LoggerInterface;
    use Psr\Log\LoggerTrait;

    class StreamLogger implements LoggerInterface
    {
        use LoggerTrait;

        /**
         * @var string $logPath
         */
        private $logPath;

        /**
         * @var StreamFactoryInterface
         */
        private $streamFactory;

        /**
         * Set log path folder and StreamFactoryInterface
         * object.
         *
         * @param string $logPath
         * @param StreamFactoryInterface $streamFactory
         * @return void
         */
        public function __construct(string $logPath, StreamFactoryInterface $streamFactory)
        {
            $this->logPath       = rtrim($logPath, '/');
            $this->streamFactory = $streamFactory;
        }

        /**
         * Write log.
         *
         * @param string $level
         * @param mixed $message
         * @param array $context
         * @return void
         */
        public function log($level, $message, array $context = [])
        {
            if (!is_string($level) || !is_string($message)) {
                throw new \InvalidArgumentException('Level or message must be a string');
            }

            $file    = $this->logPath.'/'.$level.'.log';
            $stream  = $this->streamFactory->createStreamFromFile($file, 'a+');
            $content = $this->interpolate($message."\n", $context);   
            $stream->write($content);
        }

        /**
         * Interpolates context values into the message placeholders.
         * 
         * @param string $message
         * @param array $context
         * @return string
         */
        private function interpolate($message, array $context = array())
        {
            $replace = [];
            foreach ($context as $key => $val) {
                if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                    $replace['{' . $key . '}'] = $val;
                }
            }
            $content = '['.date('Y-m-d H:i:s').'] '.$message;
            return strtr($content, $replace);
        }        
    }