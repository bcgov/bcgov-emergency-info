<?php
namespace Bcgov\Emergency_Info;

use Bcgov\Common\Tests\CommonTestCase;
use Bcgov\Emergency_Info\PublicRender;

/**
 * PublicRender Test class.
 */
class PublicRenderTest extends CommonTestCase {

    /**
     * PublicRender instance.
     *
     * @var Bcgov\Emergency_Info\PublicRender
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
