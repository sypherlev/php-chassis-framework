## Response types

Right now I have the following types built-in:

 * API - outputs a formatted JSON
 * Email - uses PHPMailer to send an email
 * File - uses readfile() to throw files at the browser
 * Web - Puts data into Twig templates
 * Cli - command line output