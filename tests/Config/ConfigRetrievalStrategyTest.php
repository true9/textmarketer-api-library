<?php

namespace True9\TextmarketerTest\Config;

use PHPUnit\Framework\TestCase;
use True9\Textmarketer\Config\ConfigRetrievalStrategy;

class ConfigRetrievalStrategyTest extends TestCase
{
    public function setUp() : void
    {
        chdir(dirname(__DIR__));
        $this->rrmdir(getcwd() . '/test-files');
        parent::setUp();
    }

    public function testConfigReaderChoosesEnvStrategyIfEnvExists()
    {
        putenv('TRUE9_TEXTMARKETER_CLIENT_CONFIG=username=test&password=test&response_type=json');

        $strategy = new ConfigRetrievalStrategy();
        $config = $strategy->loadConfig();

        $this->assertEquals('env', $strategy->getMethod());

        putenv('TRUE9_TEXTMARKETER_CLIENT_CONFIG');
    }

    public function testConfigReaderChoosesFileStrategyIfFileExists()
    {
        chdir(dirname(dirname(__FILE__)));
        mkdir(getcwd() . '/test-files/config', 0755, true);
        chdir(getcwd() . '/test-files');

        $fileContent = "<?php return [";
        $fileContent .= "'username' => 'unit-test-username',";
        $fileContent .= "'password' => 'unit-test-password'";
        $fileContent .= "];";

        file_put_contents(getcwd() . '/config/textmarketer.config.php', $fileContent);

        $strategy = new ConfigRetrievalStrategy();
        $config = $strategy->loadConfig();

        $this->assertEquals('file', $strategy->getMethod());

        chdir(__DIR__ . '/../');
        $this->rrmdir('test-files');
    }

    /**
     * Many thanks to [itay at itgoldman dot com] on the PHP man page
     * about rmdir for this recursive rmdir function
     *
     * @param string $dir
     */
     private function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir."/".$object))
                        $this->rrmdir($dir."/".$object);
                    else
                        unlink($dir."/".$object);
                }
            }
            rmdir($dir);
        }
    }
}