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

    use Embryo\Log\StreamLoggerException;
    use Embryo\Http\Factory\StreamFactory;
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
         * @var StreamFactoryInterface $streamFactory
         */
        private $streamFactory;

        /**
         * @var bool $splitByDate
         */
        private $splitByDate = true;

        /**
         * Set log path folder and StreamFactoryInterface
         * object.
         *
         * @param string $logPath
         * @param StreamFactoryInterface $streamFactory
         * @return void
         */
        public function __construct(string $logPath, StreamFactoryInterface $streamFactory = null)
        {
            $this->logPath       = rtrim($logPath, DIRECTORY_SEPARATOR);
            $this->streamFactory = ($streamFactory) ? $streamFactory : new StreamFactory;
        }

        /**
         * Split log file by date.
         * 
         * @param bool $splitByDate 
         * @return self
         */
        public function setSplitByDate(bool $splitByDate): self
        {
            $this->splitByDate = $splitByDate;
            return $this;
        }

        /**
         * Write log.
         *
         * @param string $level
         * @param mixed $message
         * @param array $context
         * @return void
         * @throws \InvalidArgumentException 
         * @throws StreamLoggerException 
         */
        public function log($level, $message, array $context = [])
        {
            if (!is_string($level) || !is_string($message)) {
                throw new \InvalidArgumentException('Level or message must be a string');
            }

            $filename = $level;
            if ($this->splitByDate) {
                $filename = date('Y-m-d').'-'.$level;
            }

            try {
                $file    = $this->logPath.DIRECTORY_SEPARATOR.$filename.'.log';
                $stream  = $this->streamFactory->createStreamFromFile($file, 'a+');
                $content = $this->interpolate($message."\n", $context);   
                $stream->write($content);
            } catch (\Exception $e) {
                throw new StreamLoggerException($e->getMessage());
            }
        }

        /**
         * Interpolates context values into the message placeholders.
         * 
         * @param string $message
         * @param array $context
         * @return string
         */
        private function interpolate(string $message, array $context = array()): string
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