<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Attachments\GenericTemplate;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Illuminate\Support\Facades\Log;



class BotManController extends Controller
{
    /**
     * Botman conversation logic
     */

    protected $store = array(
        'location' => '123 abc way New York, NY 10075',
        'hours' => '9AM - 5PM',
        'daysOpen' => 'Mon - Sat',
        'daysClosed' => 'Sat',
    );
  

    protected $firstname;
    protected $email;

    

    

    
    

     public function handle()
    {
        $botman = app('botman');
   
        // $botman->hears('{message}', function($botman, $message) {
   
        //     if ($message == 'hi') {
        //         $this->askName($botman);
        //     }else{
        //         $botman->reply("write 'hi' for testing...");
        //     }
   
        // });


        $botman->hears('feedback', function($botman) {
            $this->provideFeedback($botman);            
        });

        $botman->hears('hours', function($botman) {
            $this->provideHours($botman);
            $this->questionTemplate($botman);
            
        });

        $botman->hears('Location', function($botman) {
            $this->provideLocation($botman);
            $this->questionTemplate($botman);

        });

        $botman->hears('specials', function($botman) {
            $this->provideSpecials($botman);
            $this->questionTemplate($botman);
        });

        $botman->hears('menu', function($botman) {
            $this->provideMenu($botman);
            $this->questionTemplate($botman);
        });

        $botman->hears('ask name', function($botman) {
            $this->askFirstName($botman);
            // $this->askEmail($botman);

            // $botman->reply('hello friend');
            
        });

        $botman->hears('template', function($botman) {
            $this->template($botman);
            
        });


        $botman->hears('buttons', function($botman) {
          

            $this->questionTemplateIntial($botman);
        });

       

        $botman->hears('initial', function($botman) {
            $this->initailGreeting($botman);
            $this->askFirstName($botman);
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
                Button::create('Menu')->value('menu'),

            ]);
            $botman->reply($question);
    }

        /**
     * Questions for confirming feedback.
     */
    // public function questionTemplateFeedbackVerifacation($botman)
    // {
    //     $question = Question::create('Does that look correct to you')
    //         ->callbackId('feedback_verifactation_buttons')
    //         ->addButtons([
    //             Button::create('Sure Does!')->value('verified_feedback_response'),
    //             Button::create('Location')->value('unverified_feedback_response'),
               

    //         ]);
    //         $botman->reply($question);
    // }



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
     * Place your BotMan logic here.
     */
    public function askName($botman)
    {
        $botman->ask('Hello! What is your Name?', function(Answer $answer) {
   
            $name = $answer->getText();
            $this->say('Nice to meet you '.$name);
        });
    }

   




     /**
     * Function for capturing feed back from the bot
     */
    public function provideFeedback($botman)
    {
        $botman->ask('Thank you for taking the time to provide feedback, Please let us know how we did.', function(Answer $answer) {
            $feedback = $answer->getText();
            $this->say($feedback);
        });
    }

    /**
     * Botman function to provide store hours
     */
    public function provideHours($botman)
    {
        $botman->typesAndWaits(1);
        $botman->reply('We are open daily '.$this->store['daysOpen'].' from '.$this->store['hours'].' We are closed '.$this->store['daysClosed']);
        $botman->reply('test');
    }

    /**
     * Botman function to provide store location
     */
    public function provideLocation($botman) 
    {
        $botman->reply('We are located at '.$this->store['location']);
        // $botman->reply('test');

    }

    /**
     * Botman function to privide list of specials
     */
    public function provideSpecials($botman)
    {
        $botman->typesAndWaits(1);
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


    public function askFirstname($botman)
    {
        $botman->ask('Hello! What is your firstname?', function(Answer $answer) {
            // Save result
            $this->firstname = $answer->getText();

            $this->say('Nice to meet you '.$this->firstname);
            // Log::info($this->store);
            
        });
    }

    public function askEmail($botman){
        $botman->ask('One more thing - what is your email?', function(Answer $answer) {
            // Save result
            $this->email = $answer->getText();

            $this->say('Great - that is all we need, '.$this->firstname);
        });
    }


}