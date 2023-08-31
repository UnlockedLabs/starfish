<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LonghornOpen\LaravelCelticLTI\LtiTool;
use ceLTIc\LTI;
use app\Models\LtiProvider;


// This is all sample / test code from the LTI library
class myApp extends LTI\Tool
{

    public function onLaunch()
    {
        $tool = LtiTool::getLtiTool();
        $provider = new LtiProvider($tool->getPlatform()->getRecordId(), $tool->getContext()->getRecordId(), $tool->getUser()->getRecordId());
    }
}
class LtiController extends Controller
{
    public function getJWKS()
    {
        $tool = LtiTool::getLtiTool();
        return $tool->getJWKS();
    }
    public function ltiMessage(Request $request)
    {
        $tool = LtiTool::getLtiTool();

        $tool->handleRequest();

        if ($tool->getLaunchType() === $tool::LAUNCH_TYPE_LAUNCH) {
            // See https://github.com/celtic-project/LTI-PHP/wiki/Services for how to call LTI services such as the Names and Roles Provisioning Service.
        }

        die("Unknown message type");
    }

    // public function ltiMessage(Request $request)
    // {
    //     $tool = LtiTool::getLtiTool();
    //
    //     $tool->handleRequest();
    //
    //     $request->session()->put('context_id', $tool->context?->getRecordId());
    //     $request->session()->put('platform_id', $tool->platform?->getRecordId());
    //     $request->session()->put('user_result_id', $tool->userResult?->getRecordId());
    //
    //    // dd("Successful launch!", $tool);
    //     return "<a href='/testRoster'>Test Roster</a><br>
    //     <a href='/testLineItem'>Test Viewing Line Items</a><br>
    //     <a href='/testLineItemSet'>Test Setting Line Items</a><br>
    //     <a href='/testLineItemUpdateScore'>Test Updating Scores on Line Items</a><br>
    //     <a href='/test1'>Test 1</a><br>
    //     <a href='/test2'>Test 2</a><br>
    //     <a href='/test3'>Test 3</a><br>
    //     <a href='/test4'>Test 4</a>";
    // }

    // Dump the course's roster to the screen
    public function testRoster(Request $request)
    {
        $tool = LtiTool::getLtiTool();
        $context = $tool->getContextById(session('context_id'));
        dd($context->getMemberships());
    }

    // Find a LineItem (aka gradebook column)
    public function testLineItem()
    {
        $tool = LtiTool::getLtiTool();
        $context = $tool->getContextById(session('context_id'));
        $platform = $tool->getPlatformById(session('platform_id'));
        dd("Current line items", $context->getLineItems());
    }
    // Create a LineItem
    public function testLineItemSet()
    {
        $tool = LtiTool::getLtiTool();
        $context = $tool->getContextById(session('context_id'));
        $platform = $tool->getPlatformById(session('platform_id'));

        // create a new line item named 'Test LI' with a max-score of 100
        $lineitem = new \ceLTIc\LTI\LineItem($platform, 'Test LI', 100);
        $lineitem->label = 'LI Label';
        $lineitem->resourceId = 'LI resource ID';
        $lineitem->tag = 'LI tag';
        $lineitem->endpoint = 'LI endpoint';
        dd("Created line item?", $context->createLineItem($lineitem));
    }

    // Update a LineItem with an Outcome (aka submit a score for a student)
    public function testLineItemUpdateScore()
    {
        $tool = LtiTool::getLtiTool();
        $context = $tool->getContextById(session('context_id'));
        $platform = $tool->getPlatformById(session('platform_id'));
        $user_result = $tool->getUserResultById(session('user_result_id'));

        $line_item = $context->getLineItems()[0];
        // need to know the current grade for some reason?
        //$outcome = $line_item->readOutcome($user_result);

        $outcome = new \ceLTIc\LTI\Outcome(75, 100);
        $outcome->comment = 'Very good!';
        $ok = $line_item->submitOutcome($outcome, $user_result);

        dd("Updated score?", $ok);
    }

    public function test1()
    {
    }

    public function test2()
    {
    }

    public function test3()
    {
    }

    public function test4()
    {
    }
}
