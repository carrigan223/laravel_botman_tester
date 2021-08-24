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
        });

        $botman->hears('hours', function($botman) {
            $this->provideHours($botman);
            
        });

        $botman->hears('Location', function($botman) {
            $this->provideLocation($botman);

        });

        $botman->hears('specials', function($botman) {
            $this->provideSpecials($botman);
        });

        $botman->hears('menu', function($botman) {
            $this->provideMenu($botman);
        });

        $botman->hears('ask name', function($botman) {
            $this->askName($botman);
        });

        $botman->hears('buttons', function($botman) {
            $question = Question::create('Select a time slot')
            ->callbackId('select_time')
            ->addButtons([
                Button::create('hours')->value('hours'),
                Button::create('1 PM')->value('1 PM'),
                Button::create('3 PM')->value('3 PM'),
            ]);

            $botman->reply($question);
        });


        $botman->listen();
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