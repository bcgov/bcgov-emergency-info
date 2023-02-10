<?php
namespace Bcgov\Emergency_Info;

use Bcgov\Common\Tests\CommonTestCase;
use Bcgov\Emergency_Info\Admin;

/**
 * AdminTest class.
 */
class AdminTest extends CommonTestCase {

    /**
     * Admin instance.
     *
     * @var Bcgov\Emergency_Info\Admin
     */
    protected $admin;

    /**
     * Used for setup of each test.
     *
     * @inheritDoc
     */
    public function setUp() :void {
        parent::setUp();
        $this->admin = new Admin();
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
