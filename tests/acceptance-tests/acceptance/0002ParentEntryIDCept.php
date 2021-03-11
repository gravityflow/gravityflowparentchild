<?php
/*
 * Purpose: Test the Parent Entry ID for Child Entry
 */
use \Codeception\Util\Locator;

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the Parent Child Entry Creation' );

// Submit the form
$I->amOnPage( '/0001-child-form' );
$I->waitForText( '0001 Child Form', 3 );
$I->see( '0001 Child Form' );
$I->fillField( 'Child Field', 'Some value' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login to wp-admin
$I->loginAsAdmin();

// check child form entries
$I->amOnPage( '/wp-admin/admin.php?page=gf_entries&id=1' );
$I->waitForText( '0001 Child Form', 3 );
$I->see( '0001 Child Form' );
$I->waitForText( 'Child Field', 3 );
$I->see( 'Child Field' );
$I->see( 'Parent Entry ID: Form 2' );
$I->see( 'Child data' );
$I->see( 'Some value' );