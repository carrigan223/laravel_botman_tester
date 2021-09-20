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
  * Botman conversation logic
  */

 /** mockup of a client store object
  * temporary, will be replaced with dynamic
  * client data
  *
  */

 protected $_store = [
  'location'   => '123 abc way New York, NY 10075',
  'hours'      => '9AM - 5PM',
  'daysOpen'   => 'Mon - Sat',
  'daysClosed' => 'Sat',
 ];

 /** mockup of a client products, these are
  * temporary, will be replaced with dynamic
  * client data
  *
  */

 protected $_products = array(
  'productOne'   => array(
   'name'        => 'Lemonatti',
   'brand'       => 'Connected Cannabis Co',
   'price'       => '$29.99',
   'thcContent'  => 'THC 26.42% CBD 0.04%*',
   'productType' => 'Flower',
   'strainType'  => 'Sativa',
   'image'       => 'https://uploads.iheartjane.com/cdn-cgi/image/width=400,fit=scale-down,format=auto,metadata=none/uploads/83ef1fea-2f5c-4d4d-8ac8-da6fd2b66b2f.jpg',
   'description' => 'Lemonatti is a hybrid marijuana strain made by crossing Gelonade and Biscotti.',
  ),
  'productTwo'   => array(
   'name'        => 'Cookies',
   'brand'       => 'Arcata Fire',
   'price'       => '$56.00',
   'thcContent'  => 'THC 73.29% CBD 0.01%*',
   'productType' => 'Concentrates',
   'strainType'  => 'Indica',
   'image'       => 'https://uploads.iheartjane.com/cdn-cgi/image/width=400,fit=scale-down,format=auto,metadata=none/uploads/2dadee70-5e3d-4f44-b988-ee89606347ea.jpg',
   'description' => 'A lovely dessert strain, flavors of dark chocolate wafer and mint chip on the inhale, exhale to a gassy and sweet scent of the pine.',
  ),
  'productThree' => array(
   'name'        => 'Wild Cherry - Excite [20pk] (100mg)',
   'brand'       => 'Kiva Confections',
   'price'       => '$18.00',
   'thcContent'  => '100mg 20pk*',
   'productType' => 'Edible',
   'strainType'  => 'Sativa',
   'image'       => 'https://uploads.iheartjane.com/cdn-cgi/image/width=400,fit=scale-down,format=auto,metadata=none/uploads/ba53c492-e206-4fb3-bda7-b29cd3df8b1f.jpg',
   'description' => 'Get the rooftop party started with our Wild Cherry gummies. The invigorating blend of sativa-like terpenes with sweet, fruity notes of tart cherry will have you dancing all night long.',
  ),
 );

 /**
  * variables to hold various user information
  */

 protected $_firstname;
 protected $_email;
 protected $_phone;

 /**
  * this is the main function handling our incoming
  * post to the `/botman` route
  */

 public function handle()
 {
  $botman = app('botman');

  /**
   *  We are bringing in dilogflow middleware
   * then passing the message along to dialogFlow
   * currently we are passing everything along to
   * the middleware, we need to encapsulate the `hears`
   * commands in an if block then in the else hit the middle ware
   */

  $dialogflow = \BotMan\Middleware\DialogFlow\V2\DialogFlow::create('en');
  $botman->middleware->received($dialogflow);
  // $botman->hears('(.*)', function ($botman) {
  //     $extras = $botman->getMessage()->getExtras();
  //     $dfMessage = $botman->getMessage();
  //     // Log::info(print_r($dfMessage, true));
  //     $botman->reply($extras['apiReply']);
  // });

  /**
   * This if block is taking our message and handling it if the controller is set up to otherwise
   * we are receiving dialogFlow answer
   */

  $botman->hears('{message}', function ($botman, $message) {
   $modifiedMessage = $this->beforeDialogFlowCheck($message, $botman);
   if ($modifiedMessage == 'buttons') {
    $this->questionTemplateIntial($botman);
   } elseif ($modifiedMessage == 'card') {
    $botman->reply($this->showCards($this->_products));
    $this->anythingElseQuestion($botman);
   } elseif ($modifiedMessage == 'specials') {
    $this->specialsQuestionTemplate($botman);
   } elseif ($modifiedMessage == 'location') {
    $this->provideLocation($botman);
    $this->questionTemplate($botman);
   } elseif ($modifiedMessage == 'hours') {
    $this->provideHours($botman);
    $this->questionTemplate($botman);
   } elseif ($modifiedMessage == 'edibles') {
    foreach ($this->_products as $product) {
     if ($product['productType'] === 'Edibles') {
      $botman->reply($this->showCard($product));

     };
    };
    $this->backToInventoryQuestion($botman);
   } elseif ($modifiedMessage == 'concentrates') {
    foreach ($this->_products as $product) {
     if ($product['productType'] === 'Concentrates') {
      $botman->reply($this->showCard($product));

     };
    };
    $this->backToInventoryQuestion($botman);
   } elseif ($modifiedMessage == 'flower') {
    foreach ($this->_products as $product) {
     if ($product['productType'] === 'Flower') {
      $botman->reply($this->showCard($product));

     };
    };
    $this->backToInventoryQuestion($botman);
   } elseif ($modifiedMessage == 'done') {
    $botman->reply("Fantastic, I'm here to help if you need anything!");
   } else {
    $botman->reply($modifiedMessage);
   }
  });

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
  * function procesing incoming mesage to match to
  * our commands other wise send to dialogflow then recycle
  *
  */

 public function beforeDialogFlowCheck($message, $botman)
 {
  if ($message == 'buttons') {
   return 'buttons';
  } elseif ($message == 'card') {
   return 'card';
  } elseif ($message == 'specials') {
   return 'specials';
  } elseif ($message == 'location') {
   return 'location';
  } elseif ($message == 'hours') {
   return 'hours';
  } elseif ($message == 'edibles') {
   return 'edibles';
  } elseif ($message == 'concentrates') {
   return 'concentrates';
  } elseif ($message == 'done') {
   return 'done';
  } else {
   $extras    = $botman->getMessage()->getExtras();
   $dfMessage = $botman->getMessage();
   Log::info(print_r($dfMessage, true));
   if (count($extras['apiParameters']) > 0) {
    $newMessage = $extras['apiParameters']['return'];
   } else {
    $newMessage = $extras['apiReply'];
   }

   Log::info($newMessage);
   return $newMessage;
  }
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
