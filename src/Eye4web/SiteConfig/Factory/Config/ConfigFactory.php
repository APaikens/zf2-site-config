<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace Eye4web\SiteConfig\Factory\Config;

use Eye4web\SiteConfig\Config\Config;
use Eye4web\SiteConfig\Options\ModuleOptions;
use Eye4web\SiteConfig\Reader\ReaderInterface;
use Laminas\Config\Factory;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ConfigFactory implements FactoryInterface
{
    private $configFactory = null;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Config
     *
     * @throws \Exception
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator);
    }
    
    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null) {
        /* @var ModuleOptions $config */
        $options = $container->get(ModuleOptions::class);

        $data = null;

        $configFiles = $options->getConfigFile();
        if ($configFiles) {
            $configFactory = $this->getConfigFactory();
            if (is_array($configFiles)) {
                $data = $configFactory::fromFiles($configFiles);
            } else {
                $data = $configFactory::fromFile($configFiles);
            }
        } else {
            $reader = $container->get($options->getReaderClass());
            if ($reader instanceof ReaderInterface) {
                $data = $reader->getArray();
            } else {
                throw new \Exception('Reader must implement \Eye4web\SiteConfig\Reader\ReaderInterface');
            }
        }

        if (!is_array($data)) {
            throw new \Exception('Data for Config object must be an array');
        }

        return new Config($data);
    }

    public function getConfigFactory()
    {
        if (!$this->configFactory) {
            $this->configFactory = new Factory();
        }

        return $this->configFactory;
    }

    public function setConfigFactory($configFactory)
    {
        $this->configFactory = $configFactory;
    }
}
