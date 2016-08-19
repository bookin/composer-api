<?php
namespace bookin\composer\api;

use Composer\Console\Application;
use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryInterface;
use Composer\Repository\RepositoryManager;
use Symfony\Component\Console\Input\ArrayInput;

class Composer
{
    protected static $configFile = '';
    protected static $configFilePath = '';

    private static $composer;
    private static $app;
    private static $_instance;

    private function __construct() {}
    private function __clone() {}


    public static function getInstance($configFile=null, $configFilePath=null)
    {
        if(!isset($configFile)|| !isset($configFilePath)){
            $config = Factory::createConfig(new NullIO(), getcwd());
            if(!isset($configFile))
                $configFile = $config->get('home').'/composer.json';
            if(!isset($configFilePath))
                $configFilePath = $config->get('home');
        }
        self::$configFile = $configFile;
        self::$configFilePath = $configFilePath;

        if (null === self::$_instance)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @return \Composer\Composer
     */
    public static function getComposer(){
        if(!self::$composer){
            $factory = new Factory();
            self::$composer = $factory->createComposer(new NullIO(), self::$configFile, false, self::$configFilePath);
        }
        return self::$composer;
    }

    /**
     * @return \Composer\Package\PackageInterface[]
     */
    public static function getLocalPackages(){
        return self::getComposer()->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();
    }

    /**
     * @param $name
     * @param array $options
     * @return string
     */
    public static function updatePackage($name, $options=[]){
        return self::runCommand('update', [$name]+$options);
    }

    /**
     * @param array $options
     * @return string
     */
    public static function updateAllPackages($options=[]){
        return self::runCommand('update', $options);
    }

    /**
     * @param array $options
     * @return string
     */
    public static function deleteAllPackages($options=[]){
        return self::runCommand('remove', $options);
    }

    /**
     * @param $name
     * @param array $options
     * @return string
     */
    public static function deletePackage($name, $options=[]){
        return self::runCommand('remove', [$name]+$options);
    }



    public static function searchPackage($search){
        /* @var $app Application */
        $app = self::getApplication();
        $composer = $app->getComposer(true, false);
        $platformRepo = new PlatformRepository();
        $localRepo = $composer->getRepositoryManager()->getLocalRepository();
        $installedRepo = new CompositeRepository(array($localRepo, $platformRepo));
        $repos = new CompositeRepository(array_merge(array($installedRepo), $composer->getRepositoryManager()->getRepositories()));
        $flags = RepositoryInterface::SEARCH_FULLTEXT;
        $results = $repos->search($search, $flags);

        return $results;
    }

    public static function findPackage($name, $version=null)
    {
        if (strpos($name, '/') === false) {
            throw new \Exception('You need use full package name: vendor/vendor1');
        }
        /** @var RepositoryManager $repositoriManager */
        $repositoryManager = self::getComposer()->getRepositoryManager();
        $package = $repositoryManager->findPackage($name, $version);
        return $package;
    }


    public static function getApplication(){
        if(empty(self::$app)){
            $app = new WebApplication();
            $app->setComposer(self::getComposer());
            $app->setAutoExit(false);
            self::$app = $app;
        }
        return self::$app;
    }

    /**
     * @param string $command
     * @param array $params
     * @return string
     */
    public static function runCommand($command='', $params=[]){

        if(empty($command)){
            $command='list';
        }

        $parameters = ['command'=>$command]+$params;

        $input = new ArrayInput($parameters);
        $output = new ComposerOutput();

        $output->setFormatter(new BootstrapOutputFormatter());

        try {
            $app = self::getApplication();
            $app->run($input, $output);
        }catch (\Exception $c){
            $output->write($c->getMessage());
        }

        return $output->getMessage();
    }
}