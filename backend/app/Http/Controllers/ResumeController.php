<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use OpenAI;

class ResumeController extends Controller
{
    public function analyze(Request $request)
    {
        $request->validate([
            'resume' => 'required|file|mimes:pdf|max:2048',
        ]);

        $file = $request->file('resume');
        $resumesDir = storage_path('app/resumes');
        if (!file_exists($resumesDir)) mkdir($resumesDir, 0777, true);

        $extension = $file->getClientOriginalExtension();
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $uniqueName = $safeName . '_' . time() . '.' . $extension;
        $fullPath = $resumesDir . DIRECTORY_SEPARATOR . $uniqueName;
        $file->move($resumesDir, $uniqueName);

        $parser = new Parser();
        $text = $parser->parseFile($fullPath)->getText();

        $client = OpenAI::factory()
        ->withApiKey(env('OPENAI_API_KEY'))
        ->withHttpClient(new \GuzzleHttp\Client(['verify' => false]))
        ->make();

        $result = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => "You are a resume analyst. Your task is to highlight the strong points and weak points of resumes. 
                Return the response ONLY as clean HTML (without <html> or <body> tags). 
                Use headings (<h2>, <h3>) for sections, <ul><li> for lists, and <p> for explanations. 
                Keep it structured, professional, and easy to read."],
                ['role' => 'user', 'content' => $text], // <-- aqui entra o texto extraído do PDF
            ],
        ]);

            $analysis = $result->choices[0]->message->content;

        return response()->json([
            'message' => 'Resume uploaded and analyzed successfully',
            'file_path' => 'resumes/' . $uniqueName,
            'extracted_text' => $text,
            'analysis' => $analysis,
        ], 200);
    }
}
