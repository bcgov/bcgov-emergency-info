<?php
namespace Bcgov\Plugin_Name;

use Bcgov\Common\Tests\CommonTestCase;
use Bcgov\Plugin_Name\Admin;

/**
 * AdminTest class.
 */
class AdminTest extends CommonTestCase {

    /**
     * Admin instance.
     *
     * @var Bcgov\Plugin_Name\Admin
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
