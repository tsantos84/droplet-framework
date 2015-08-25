<?php

namespace spec\Framework\Config;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Config\FileLocatorInterface;

class FileLoaderSpec extends ObjectBehavior
{
    public function let(FileLocatorInterface $locator)
    {
        $this->beConstructedWith($locator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Framework\Config\FileLoader');
    }

    function it_implements_FileLoaderInterface()
    {
        $this->shouldHaveType('Symfony\Component\Config\Loader\LoaderInterface');
    }

    function it_supports_php_resources()
    {
        $this->supports('file.php')->shouldReturn(true);
    }

    function it_does_not_support_any_other_kind_of_resources()
    {
        $this->supports('file.php.xml')->shouldReturn(false);
    }

    function it_can_load_a_simple_php_file(FileLocatorInterface $locator)
    {
        $file = 'spec/Resources/config/config.php';

        $locator->locate($file)->willReturn($file);

        $this->load($file)->shouldReturn([
            'core' => [
                ['option_1' => 'value_1']
            ]
        ]);
    }

    function it_can_load_a_php_file_that_imports_another_php_file(FileLocatorInterface $locator)
    {
        $prod = 'spec/Resources/config/config_prod.php';
        $config = 'spec/Resources/config/config.php';

        $locator->locate($prod)->willReturn($prod);

        $locator->locate(
            Argument::containingString('config.php'),
            Argument::any(),
            Argument::any()
        )->willReturn($config);

        $this->load($prod)->shouldReturn([
            'core' => [
                ['option_1' => 'value_1'],
                [
                    'option_1' => 'value_1',
                    'option_2' => 'value_2'
                ]
            ]
        ]);
    }

    function it_should_not_allow_loading_an_invalid_php_file(FileLocatorInterface $locator)
    {
        $file = 'spec/Resources/config/invalid_config.php';
        $locator->locate($file)->willReturn($file);

        $this->shouldThrow(new \InvalidArgumentException('The configuration file "' . $file . '" must return an array'))->duringLoad($file);
    }
}
