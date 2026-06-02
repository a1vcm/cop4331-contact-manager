<?php

// ─── CORS ────────────────────────────────────────────────────────────────────
// Allow your Vercel frontend to call this API.
// Change the origin below if your domain ever changes.
header('Access-Control-Allow-Origin: https://cop4331-summer2026-17.xyz');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Pre-flight request — browser sends this before the real POST
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ─── Helpers ─────────────────────────────────────────────────────────────────

/**
 * Read the raw JSON body that the frontend sends.
 */
function getRequestInfo()
{
    return json_decode(file_get_contents('php://input'), true);
}

/**
 * Send any array / object back as JSON.
 * Using json_encode() instead of manual string building so special
 * characters in names (apostrophes, quotes, etc.) never break the response.
 */
function sendResultInfoAsJson($obj)
{
    header('Content-Type: application/json');
    echo json_encode($obj);
}

// ─── Response builders ───────────────────────────────────────────────────────

function returnWithError($err)
{
    sendResultInfoAsJson(['error' => $err]);
}

function returnWithRegistrationInfo($id)
{
    sendResultInfoAsJson(['id' => (int)$id, 'error' => '']);
}

function returnWithLoginInfo($firstName, $lastName, $id)
{
    sendResultInfoAsJson([
        'id'        => (int)$id,
        'firstName' => $firstName,
        'lastName'  => $lastName,
        'error'     => ''
    ]);
}