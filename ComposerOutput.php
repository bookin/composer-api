<?php
namespace bookin\composer\api;

#use Composer\Composer;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Output\Output;

/**
 * Class ComposerOutput
 *
 * @property string $message
 * @property OutputFormatter $formatter
 */
class ComposerOutput extends Output
{
    protected $message;

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return nl2br($this->message);
    }

    /**
     * Empties buffer and returns its content.
     *
     * @return string
     */
    public function fetch()
    {
        $message = $this->message;
        $this->message = '';

        return $message;
    }


    /**
     * Writes a message to the output.
     *
     * @param string $message A message to write to the output
     * @param bool $newline Whether to add a newline or not
     */
    protected function doWrite($message, $newline)
    {
        if(strripos($message, \Composer\Composer::BRANCH_ALIAS_VERSION) === false){
            $this->message .= ($newline?'<br>':'').$message;
        }
    }
}