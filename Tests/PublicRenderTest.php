<?php
namespace Bcgov\Plugin_Name;

use Bcgov\Common\Tests\CommonTestCase;
use Bcgov\Plugin_Name\PublicRender;

/**
 * PublicRender Test class.
 */
class PublicRenderTest extends CommonTestCase {

    /**
     * PublicRender instance.
     *
     * @var Bcgov\Plugin_Name\PublicRender
     */
    protected $public;

    /**
     * Used for setup of each test.
     *
     * @inheritDoc
     */
    public function setUp() :void {
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
