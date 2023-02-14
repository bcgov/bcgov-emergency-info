<?php
namespace Bcgov\EmergencyInfo;

use Bcgov\Common\Tests\CommonTestCase;
use Bcgov\EmergencyInfo\PublicRender;
use Brain\Monkey\Functions;

/**
 * PublicRender Test class.
 */
class PublicRenderTest extends CommonTestCase {

    /**
     * PublicRender instance.
     *
     * @var Bcgov\EmergencyInfo\PublicRender
     */
    protected $public;

    /**
     * Used for setup of each test.
     *
     * @inheritDoc
     */
    public function setUp() :void {
        Functions\when( 'is_admin' )->justReturn( false );
        parent::setUp();
        $this->public = new PublicRender();
    }


    /**
     * Initial Test.
     *
     * @return void
     */
    public function testInit() {
        $results = '';
        $this->assertEquals( $results, '' );
    }
}
