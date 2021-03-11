<?php
/*
 * Purpose: Test the Parent Child Entry Creation
 */
use \Codeception\Util\Locator;

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the Parent Child Entry Creation' );

// Submit the form
$I->amOnPage( '/0001-parent-form' );
$I->waitForText( '0001 Parent Form', 3 );
$I->see( '0001 Parent Form' );
$I->fillField( 'Parent Field', 'Parent data' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login to wp-admin
$I->loginAsAdmin();

// make child-parent form relation
$I->amOnPage( '/wp-admin/admin.php?page=gf_edit_forms&view=settings&subview=gravityflowparentchild&id=1' );
$I->checkOption( '0001 Parent Form' );
$I->click( 'Update Settings' );
$I->waitForText( 'Parent-Child Forms settings updated.', 3 );
$I->see( 'Parent-Child Forms settings updated.' );

// check parent form entries
$I->amOnPage( '/wp-admin/admin.php?page=gf_entries&id=2' );
$I->waitForText( '0001 Parent Form', 3 );
$I->see( '0001 Parent Form' );
$I->waitForText( 'Parent Field', 3 );
$I->see( 'Parent Field' );
$I->see( 'Parent data' );
$I->click( 'Parent data' );
$I->waitForText( '0001 Parent Form : Entry #', 3 );
$I->see( '0001 Parent Form : Entry #' );

// add a child entry on parent form
$I->see( '0001 Child Form' );
$I->click( '//*[@id="postbox-container-1"]/div[2]/h3/span[2]/a' );
$I->waitForText( '0001 Child Form', 3 );
$I->see( '0001 Child Form' );
$I->fillField( 'Child Field', 'Child data' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// check parent form entry contains the child form entry
$I->amOnPage( '/wp-admin/admin.php?page=gf_entries&id=2' );
$I->waitForText( '0001 Parent Form', 3 );
$I->see( '0001 Parent Form' );
$I->waitForText( 'Parent Field', 3 );
$I->see( 'Parent Field' );
$I->see( 'Parent data' );
$I->click( 'Parent data' );

$I->waitForText( '0001 Parent Form : Entry #', 3 );
$I->see( '0001 Parent Form : Entry #' );

// check if the child form entry found on the parent form
$I->see( '0001 Child Form' );
$I->click( '//*[@id="minor-publishing"]/div/table/tbody/tr/td[1]/a' );
$I->waitForText( '0001 Child Form', 3 );
$I->see( '0001 Child Form' );
$I->see( '0001 Child Form : Entry #' );
$I->see( 'Child Field' );
$I->see( 'Child data' );