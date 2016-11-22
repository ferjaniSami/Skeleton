<?php
namespace ArtistTest\Controller;

use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ArtistControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    public function setUp()
    {
        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
            // Grabbing the full application configuration:
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));
        parent::setUp();
    }
	
	public function testIndexActionCanBeAccessed()
	{
		$this->dispatch('/artist');
		$this->assertResponseStatusCode(200);
		$this->assertModuleName('Artist');
		$this->assertControllerName(ArtistController::class);
		$this->assertControllerClass('ArtistController');
		$this->assertMatchedRouteName('artist');
	}
	
	public function testIndexAction()
{
    $this->dispatch('/', 'POST', array('argument' => 'value'));
}
}