<?php

use Sagautam5\EmailBlocker\Supports\EmailContext;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

beforeEach(function () {
    $this->email = new Email;
    $this->email->from(new Address('from@example.com', 'Sender Name'));
    $this->email->to('to@example.com');
    $this->email->cc('cc@example.com');
    $this->email->bcc('bcc@example.com');
    $this->email->subject('Test Subject');
    $this->email->text('Text body');
    $this->email->html('<p>HTML body</p>');
});

it('returns null for mailable class if none is provided', function () {
    $context = new EmailContext($this->email);
    expect($context->getMailableClass())->toBeNull();
});

it('returns from email and name correctly', function () {
    $context = new EmailContext($this->email);
    expect($context->getFromEmail())->toBe('from@example.com')
        ->and($context->getFromName())->toBe('Sender Name');
});

it('returns the subject correctly', function () {
    $context = new EmailContext($this->email);
    expect($context->getSubject())->toBe('Test Subject');
});

it('returns the body with preference for HTML', function () {
    $context = new EmailContext($this->email);
    expect($context->getBody())->toBe('<p>HTML body</p>');
});

it('falls back to text body if HTML is not available', function () {
    $email = new Email;
    $email->text('Just text body');
    $context = new EmailContext($email);
    expect($context->getBody())->toBe('Just text body');
});

it('returns all "to" emails', function () {
    $context = new EmailContext($this->email);
    expect($context->getToEmails())->toBe(['to@example.com']);
});

it('returns all "cc" emails', function () {
    $context = new EmailContext($this->email);
    expect($context->getCcEmails())->toBe(['cc@example.com']);
});

it('returns all "bcc" emails', function () {
    $context = new EmailContext($this->email);
    expect($context->getBccEmails())->toBe(['bcc@example.com']);
});

it('handles empty recipients gracefully', function () {
    $email = new Email;
    $context = new EmailContext($email);

    expect($context->getToEmails())->toBe([])
        ->and($context->getCcEmails())->toBe([])
        ->and($context->getBccEmails())->toBe([]);
});
