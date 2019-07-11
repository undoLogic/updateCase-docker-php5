<?php
// app/Test/Case/View/Helper/CurrencyRendererHelperTest.php

App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('MinifyHelper', 'View/Helper');

class MinifyHelperTest extends CakeTestCase
{
    public $Minify = null;

    // Here we instantiate our helper
    public function setUp() {
        parent::setUp();
        $Controller = new Controller();
        $View = new View($Controller);
        $this->Minify = new MinifyHelper($View);
    }

    public function testScriptCleanup() {

        $textOriginal = "<script> this is script test
</script>";

        $textShouldBe = "<script> this is script test</script>";

        //debug ($textOriginal);

        //debug ($textShouldBe);

      //exit;


      $this->assertEquals(
       $textShouldBe,
         $this->Minify->clean($textOriginal)
      );

        //exit;

    }


    // Testing the usd() function
    public function testJavascriptCleanup() {

        $textOriginal = "
<script>

function test() {
    
    var b = 6;
    
    var c = 7;
    
    alert(b + c);
}

</script>
    ";

        $textShouldBe = "<script>function test() {var b = 6;var c = 7;alert(b + c);}</script>";

        debug ($textOriginal);

        debug ($textShouldBe);

        //exit;


        $this->assertEquals(
            $textShouldBe,
            $this->Minify->clean($textOriginal)
        );

        //exit;

    }




    // Testing the usd() function
    public function testHtmlCleanupEasy() {

        $textOriginal = "      <table height=\"230px\" 
background=\"images/header1.jpg\">
            <tr>     <td>Some goes here with spaces&nbsp;between</td>
            </tr></table> ";

        $textShouldBe = "<table height=\"230px\" background=\"images/header1.jpg\"><tr><td>Some goes here with spaces&nbsp;between</td></tr></table>";

        //debug ($textOriginal);

        //debug ($textShouldBe);


        $this->assertEquals(
            $textShouldBe,
            $this->Minify->clean($textOriginal)
        );

        //exit;

    }

    // Testing the usd() function
    public function testDivCleanup() {

        $textOriginal = "    <div class='test' style='background-color: RED'>          <span class=\"borders\">Text here should keep spaces
    </span>
            
            </div>";

        $textShouldBe = "<div class='test' style='background-color: RED'><span class=\"borders\">Text here should keep spaces</span></div>";

        debug ($textOriginal);

        debug ($textShouldBe);

        //exit;


        $this->assertEquals(
            $textShouldBe,
            $this->Minify->clean($textOriginal)
        );

        //exit;

    }


    public function testHCleanup() {

        $textOriginal = "    <div class='test' style='background-color: RED'>          <span class=\"borders\">Text here should keep spaces
    </span>
            
            </div>";

        $textShouldBe = "<div class='test' style='background-color: RED'><span class=\"borders\">Text here should keep spaces</span></div>";

        debug ($textOriginal);

        debug ($textShouldBe);

        //exit;


        $this->assertEquals(
            $textShouldBe,
            $this->Minify->clean($textOriginal)
        );

        //exit;

    }







}