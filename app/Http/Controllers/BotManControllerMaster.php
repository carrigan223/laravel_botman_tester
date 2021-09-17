<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Facades\Log;

class BotManController extends Controller
{
 /**
  * Begining of Botman Conversation Logic
  */

 /**
  * `function` handle is receiving our incoming message for botman
  *  to handle
  */

 public function handle()
 {
  /**
   * `$botman` is Initiating instance of botman
   */
  $botman = app('botman');

  /**
   * Creating instance of dialogFlow middleWare we are then passing
   * the commands through dialogFlow is listening for
   */
  $dialogflow = \BotMan\Middleware\DialogFlow\V2\DialogFlow::create('en');
  $botman->middleware->received($dialogflow);

  /**
   * Passing the string value `$botman` hears to be evaluated in if block,
   * if the controller can handle the reply it will be executed. Otherwise we
   * will turn to dialogflow to provide a response.
   */
    $botman->hears('{message}', function ($botman, $message) {

    if ($message == 'hi') {
     $botman->reply("you said hi");
    } elseif ($message == 'no') {
    $botman->reply("you said no");
    }
    else {
     $extras = $botman->getMessage()->getExtras();
     $botman->reply($extras['apiReply']);
    }

    

   });


   /**
    * when outside of botman studio listen MUST be called at and of the handle function
    */
   $botman->listen();

 }

 /**
  * Template for initial call to questions.
  */
  public function questionTemplateIntial($botman)
  {
   $question = Question::create('')
    ->callbackId('guide_buttons')
    ->addButtons([
     Button::create('Hours')->value('hours'),
     Button::create('Location')->value('location'),
     Button::create('Feedback')->value('feedback'),
     Button::create('Specials')->value('specials'),
     Button::create('Menu')->value('card'),

    ]);
   $botman->reply($question);
  }

  /**
   *
   * function for follow up anything else questions
   */

  public function anythingElseQuestion($botman)
  {
   $question = Question::create('Anything else I can help you with?')
    ->callbackId('anything_else_questions')
    ->addButtons([
     Button::create('Yes')->value('buttons'),
     Button::create('No')->value('anything'),
    ]);
   $botman->reply($question);
  }

  /**
   *
   * function for viewing more specials up anything else questions
   */

  public function backToInventoryQuestion($botman)
  {
   $question = Question::create('Would you like to view more products?')
    ->callbackId('more_product_questions')
    ->addButtons([
     Button::create('Yes')->value('specials'),
     Button::create('No')->value('done'),
    ]);
   $botman->reply($question);
  }

  /**
   * Template for callback to questions.
   */
  public function questionTemplate($botman)
  {
   $question = Question::create('What else can I help you with?')
    ->callbackId('select_time')
    ->addButtons([
     Button::create('Hours')->value('hours'),
     Button::create('Location')->value('location'),
     Button::create('Feedback')->value('feedback'),
     Button::create('Specials')->value('specials'),
     Button::create('Menu')->value('menu'),

    ]);
   $botman->reply($question);
  }

  /**
   * Creating the Buttons to navigate ypes of specials.
   */
  public function specialsQuestionTemplate($botman)
  {
   $question = Question::create('What type of specials are you looking for?')
    ->callbackId('specials_types')
    ->addButtons([
     Button::create('Flower')->value('flower'),
     Button::create('Concentrates')->value('concentrates'),
     Button::create('Edibles')->value('edibles'),
     Button::create('All')->value('card'),
    ]);
   $botman->reply($question);
  }

  /**
   * Place your BotMan logic here.
   */
  public function askName($botman)
  {
   $botman->ask('Hello! What is your Name?', function (Answer $answer) {

    $name = $answer->getText();
    $this->say('Nice to meet you ' . $name);
   });
  }

  /**
   * Function for capturing feed back from the bot
   */
  public function provideFeedback($botman)
  {
   $botman->ask('Thank you for taking the time to provide feedback, Please let us know how we did.', function (Answer $answer) {
    $feedback = $answer->getText();
    $this->say($feedback);
   });
  }

  /**
   * Botman function to provide _store hours
   */
  public function provideHours($botman)
  {
   $botman->reply('We are open daily ' . $this->_store['daysOpen'] . ' from ' . $this->_store['hours'] . ' We are closed ' . $this->_store['daysClosed']);
   $botman->reply('test');
  }

  /**
   * Botman function to provide _store location
   */
  public function provideLocation($botman)
  {
   $botman->reply('We are located at ' . $this->_store['location']);
   // $botman->reply('test');

  }

  /**
   * Botman function to privide list of specials
   */
  public function provideSpecials($botman)
  {
   $botman->reply('These are our specials');
  }

  /**
   * Botman response to provide menu info
   */
  public function provideMenu($botman)
  {
   $botman->typesAndWaits(1);
   $botman->reply('this is the menu');
  }

  public function initailGreeting($botman)
  {
   $botman->typesAndWaits(1);
   $botman->reply('Im here to start to make your experience with Buzz a little easier');
   $botman->reply('Lets start with some of your info so I can better help you');

  }

  /**
   * function `askFirstName` takes in `$botman`
   * to then use the `$botman` ask method to
   * ask a question and collect the answer
   */

  public function askFirstname($botman)
  {
   $botman->ask('Hello! What is your firstname?', function (Answer $answer) {
    // Save result
    $this->firstname = $answer->getText();

    $this->say('Nice to meet you ' . $this->firstname);
    // Log::info($this->_store);

   });
  }

  /**
   * function `askEmail` takes in `$botman`
   * to then use the `$botman` ask method to
   * ask a question and collect the answer
   */

  public function askEmail($botman)
  {
   $botman->ask('One more thing - what is your email?', function (Answer $answer) {
    // Save result
    $this->email = $answer->getText();

    $this->say('Great - that is all we need, ' . $this->firstname);
   });
  }

  /**
   * template function for the HTML to show a single
   * card
   */

  public function showCard($product)
  {
   return "
             <style>
             /* Tooltip container */
             .tooltip {
             position: relative;
             display: inline-block;
             border: none;
             }

             /* Tooltip text */
             .tooltip .tooltiptext {
                 opacity: 0;
             transition: opacity 1s;
             visibility: hidden;
             width: 120px;
             background: rgba(0,0,0,0.6);
             color: white;
             text-align: center;
             padding: 5px 0;
             border-radius: 6px;
             line-height: 1.5rem;


             /* Position the tooltip text - see examples below! */
             position: absolute;
             z-index: 1;
             }

             /* Show the tooltip text when you mouse over the tooltip container */
             .tooltip:hover .tooltiptext {
             visibility: visible;
             opacity: 1;
             }
             </style>
                 <div style='
                         height: 350px;
                         width: fit-content;
                         display: flex;
                         flex-direction: column;
                         padding: 10px;
                         background: white;
                         border-radius: 3px;
                         margin: 0px 10px;
                         box-shadow: rgba(50, 50, 93, 0.25) 0px 10px 35px -20px, rgba(0, 0, 0, 0.3) 0px 10px 25px -30px, rgba(10, 37, 64, 0.35) 0px -2px 6px 0px inset;
                     '>
                     <div class='tooltip' style='border-radius: 3px; box-shadow: 0px 0px 10px lightgrey'>
                         <img style='width: 150px; height: 150px; border-radius: 3px' src=" . $product['image'] . " />
                         <span class='tooltiptext'>" . $product['description'] . "</span>
                     </div>
                     <div style='
                             display: flex;
                             flex-direction: column;
                             justify-content: center;
                             align-items: center;
                         '>
                         <h3 style='text-transform: uppercase; text-align: center;'>
                             " . $product['name'] . "
                         </h3>
                         <span style='text-transform: uppercase; padding: 3px; text-align: center;'>" . $product['brand'] . "</span
                         >
                         <span style='text-transform: uppercase; padding: 3px'
                             >" . $product['productType'] . "</span
                         >
                         <span style='text-transform: uppercase; padding: 3px'
                             >" . $product['strainType'] . "</span
                         >
                         <span style='text-shadow: 1px 1px 2px grey; padding: 3px; text-align: center;'
                             >" . $product['thcContent'] . "</span
                         >
                         <span style='padding: 0px'>" . $product['price'] . "</span>
                     </div>
                 </div>
             ";
  }

  /*
   * function to show multiple cards as carousel,
   * if more then one product is comming in
   * we are appending an extra div to contain
   * the cards as a carousel
   */

  public function showCards($_products)
  {
   $html = "<div style='display: flex; overflow-x: scroll; overflow-y: visible;height: max-content;'>";
   foreach ($_products as $product) {
    $html .= $this->showCard($product);
   }
   $html .= "</div>";

   return $html;

  }
}
