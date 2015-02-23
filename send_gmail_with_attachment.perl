#!/usr/bin/perl

# install perl module under pfSense with
# 1. setenv PACKAGESITE to matching pfSense version
# 2. pkg_add -r p5-Net-SMTP-TLS
# edit /usr/local/lib/perl5/site_perl/5.12.4/Net/SMTP/TLS.pm as in http://www.perlmonks.org/?node_id=929055

use Net::SMTP::TLS;

my $SUBJECT=$ARGV[0];
my $DATA_FILE = $ARGV[1];
my $FILENAME=$ARGV[2];
my $CONTENTTYPE=$ARGV[3];
my $BODY=$ARGV[4];
my $SENDER=$ARGV[5];
my $PASSWORD=$ARGV[6];
my $RECEIVER=$ARGV[7];

my $attachFile = 'attachment';
my $boundary = 'frontier';

open(DATA, $DATA_FILE) || die("Could not open the file");
binmode FILE;
my ($buf, $data, $n);
while (($n = read DATA, $data, 4) != 0) {
  $buf .= $data;
}
close(DATA);

my $SMTP='smtp.gmail.com';
my $HELLO='smtp.gmail.com';
my $PORT=587;

my $mailer = new Net::SMTP::TLS(
  $SMTP,
  Hello =>$HELLO,
  Port=>$PORT,
  User=>$SENDER,
  Password=>$PASSWORD);
$mailer->mail($SENDER);
$mailer->to($RECEIVER);
$mailer->data;
$mailer->datasend("From: " . $SENDER . "\n");
$mailer->datasend("To: " . $RECEIVER . "\n");
$mailer->datasend("Subject: " . $SUBJECT . "\n");
$mailer->datasend("MIME-Version: 1.0\n");
$mailer->datasend("Content-type: multipart/mixed;\n\tboundary=\"$boundary\"\n");
$mailer->datasend("\n");
$mailer->datasend("--$boundary\n");
$mailer->datasend("Content-type: text/plain\n");
$mailer->datasend("Content-Disposition: quoted-printable\n");
$mailer->datasend("\n$BODY\n\n");
$mailer->datasend("--$boundary\n");
$mailer->datasend("Content-Type: $CONTENTTYPE; name=\"$FILENAME\"\n");
$mailer->datasend("Content-Disposition: attachment; filename=\"$FILENAME\"\n");
$mailer->datasend("\n");
$mailer->datasend("$buf\n");
$mailer->datasend("--$boundary--\n");
$mailer->dataend();
$mailer->quit;

