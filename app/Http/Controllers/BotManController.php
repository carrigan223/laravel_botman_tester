<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class BotManController extends Controller
{
    /**
     * Botman conversation logic
     */
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
            $this->questionTemplate($botman);
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
            $this->askName($botman);
            
        });

        $botman->hears('buttons', function($botman) {
          

            $this->questionTemplateIntial($botman);
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
   
            $this->say('Verify Your Message: '.$feedback);
        });
    }

    /**
     * Botman function to provide store hours
     */
    public function provideHours($botman)
    {
        $botman->reply('We are open daily Mon-Sat from 9AM - 5PM. We are closed Sunday ');
        
    }

    /**
     * Botman function to provide store location
     */
    public function provideLocation($botman) 
    {
        $botman->reply('You can visit us at 7128 Miramar Rd, San Diego, CA 92121.');

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
        $botman->reply('this is the menu');

    }
}