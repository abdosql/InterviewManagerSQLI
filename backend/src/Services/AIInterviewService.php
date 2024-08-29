<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Services;

use App\AI\Facade\AIFacade;

readonly class AIInterviewService
{
    public function __construct(
        private AIFacade $aiFacade
    ) {}

    public function generateInterviewFeedback(string $interviewSummary): string
    {
        $prompt = "
            You are an expert in human resources and professional development, familiar with the Moroccan education system. Your task is to analyze the following interview summary and provide constructive reformulations of the evaluator's appreciations for the candidate. 
            
            For each appreciation in the summary:
            1. Carefully consider the evaluator's original comment and score.
            2. Reformulate the comment to be more constructive, specific, and actionable, while maintaining the essence of the original feedback.
            3. Review the original score and recalibrate it to a fair score out of 20, as used in the Moroccan exam system. Ensure this score accurately reflects the reformulated comment and the candidate's performance.
            4. Format your response as a JSON object with two keys: 'comment' and 'score'.
            
            Rules:
            - Provide only the JSON response for each appreciation, without any additional explanation or text.
            - Ensure the 'comment' is a string and the 'score' is a number between 0 and 20, allowing for decimal points if necessary (e.g., 15.5).
            - Maintain a professional and constructive tone in your reformulations.
            - Focus on providing specific, actionable feedback that the candidate can use for improvement.
            - Ensure the score is fair and aligns with the Moroccan scoring system, where 20 is the highest possible score.
            
            Example format for each appreciation:
            {
              'comment': 'Your reformulated comment here',
              'score': 16.5
            }
            
            Please process the following interview summary and provide your reformulated appreciations in the specified JSON format, with scores adjusted to the Moroccan 0-20 scale.:\n\n" .

            $interviewSummary;
        return $this->aiFacade->getAIResponse($prompt);
    }
}