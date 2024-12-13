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



    public function generateInterviewReformulation(string $interviewSummary): array
    {
        $prompt = "
        You are an expert in human resources and professional development, familiar with the Moroccan education system. Your task is to analyze the following interview summary, provide a well-structured, creatively formatted reformulation of the evaluator's appreciations for the candidate, and then assign a score based on your reformulation.

        Process:
        1. Analyze the interview summary.
        2. Create a comprehensive reformulation that covers all aspects mentioned in the interview summary.
        3. Format your reformulation using only HTML tags and styles supported by the Froala text editor.
        4. Based on your reformulation, not the original summary, assign a strict and precise overall score out of 20, reflecting the Moroccan scoring system.
        5. Detect the language used in the interview summary and use the same language for your reformulation.

        Rules:
        - Use only HTML tags and inline styles that are fully supported by the Froala text editor.
        - Create an engaging, visually appealing layout within the constraints of Froala's supported features.
        - Maintain a professional and constructive tone, providing specific and actionable feedback.
        - Be strict and precise with the score, ensuring it accurately represents the candidate's performance as described in your reformulation.

        Your response should be in this exact JSON format:
        {
          'comment': 'Your Froala-compatible formatted HTML comment here',
          'score': 16.5,
          'language': 'The detected language (e.g., English, French, Arabic)'
        }

        Froala-supported HTML Formatting Guidelines:
        - Use heading tags (h1, h2, h3, h4) for different levels of information
        - Utilize <p> tags for paragraphs
        - Use <strong> and <em> tags for emphasis
        - Create lists with <ul>, <ol>, and <li> tags
        - Use <br> for line breaks
        - Add basic inline styles like 'color', 'background-color', 'font-size', and 'font-family'
        - Use <span> tags for inline text styling
        - Create tables with <table>, <tr>, <td> tags if needed
        - Use <a> tags for links (though they may not be necessary in this context)

        Scoring Guidelines (to be applied to your reformulation):
        - 18-20: Exceptional performance across all areas
        - 16-17: Very strong performance with minor areas for improvement
        - 14-15: Good performance with clear strengths and some areas for development
        - 12-13: Satisfactory performance with balanced strengths and weaknesses
        - 10-11: Basic competence demonstrated, significant room for improvement
        - Below 10: Substantial gaps in required skills and knowledge

        Example structure (but with your own content based on the interview summary):
        {
          'comment': '<h2 style=\"color: #4a86e8;\">Technical Skills Assessment</h2><p><strong>Symfony and Backend Development:</strong></p><ul><li style=\"color: #006400;\">Highly proficient in Symfony, demonstrated by multiple certifications</li><li>Successfully implemented CQRS architecture in projects</li><li style=\"color: #FFA500;\">Area for growth: Explore advanced Symfony features and optimizations</li></ul><p><strong>Full Stack Capabilities:</strong></p><ul><li>Competent in both backend (Laravel, Flask) and frontend (React) technologies</li><li style=\"color: #FFA500;\">Recommendation: Deepen expertise in a specific JavaScript framework</li><li>Demonstrated ability to implement key features using various technologies</li></ul><p><em>Overall, the candidate shows strong technical foundations with clear areas for continued professional growth.</em></p>',
          'score': 16.5,
          'language': 'English'
        }

        Please process the following interview summary, provide your reformulated appreciation in the specified JSON format with Froala-compatible HTML formatting, assign a score based on your reformulation, and indicate the detected language:\n\n" .

            $interviewSummary;
        return $this->aiFacade->getAIResponse($prompt);
    }
}