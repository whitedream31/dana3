<?php
namespace dana\table;

//use dana\core;

require_once 'class.basetable.php';

/**
  * rating table
  * @version dana framework v.3
*/

class rating extends idtable {
  const RATE_EXCELLENT = 5;
  const RATE_VERYGOOD = 4;
  const RATE_GOOD = 3;
  const RATE_POOR = 2;
  const RATE_BAD = 1;
  const RATE_NA = 0;

  const RATINGTYPE_QUALITY = 'quality';
  const RATINGTYPE_COST = 'cost';
  const RATINGTYPE_WAITING = 'waiting';
  const RATINGTYPE_AFTERSALE = 'aftersale';
  const RATINGTYPE_PROFESSIONAL = 'professional';
  const RATINGTYPE_CONTACT = 'contact';
  const RATINGTYPE_GENERAL = 'general';
  const RATINGTYPE_OVERALL = 'overall';
  const RATINGTYPE_COUNT = 'count';
  const RATINGTYPE_COMMENTS = 'comments';

  public $accountid;
  public $ipaddress;
  public $visitorid;
  public $visitorname;
  public $valuequality;
  public $valuecost;
  public $valuewaiting;
  public $valueaftersale;
  public $valueprofessional;
  public $valuecontact;
  public $valuegeneral;
  public $valueoverall;
  public $comment;
  public $commentstamp;
  public $reply;
  public $replystamp;
  public $status;

  public $account;
  public $accountexists;
  public $available;
  public $title;
  public $dostats = true;
  static public $statistics = false;
  public $rated = false;

  function __construct($id = 0) {
    parent::__construct('rating', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->accountid = $this->AddField(self::FN_ACCOUNTID, self::DT_FK);
    $this->ipaddress = $this->AddField('ipaddress', self::DT_STRING);
    $this->visitorid = $this->AddField('visitorid', self::DT_FK);
    $this->visitorname = $this->AddField('visitorname', self::DT_STRING);
    $this->valuequality = $this->AddField('valuequality', self::DT_INTEGER);
    $this->valuecost = $this->AddField('valuecost', self::DT_INTEGER);
    $this->valuewaiting = $this->AddField('valuewaiting', self::DT_INTEGER);
    $this->valueaftersale = $this->AddField('valueaftersale', self::DT_INTEGER);
    $this->valueprofessional = $this->AddField('valueprofessional', self::DT_INTEGER);
    $this->valuecontact = $this->AddField('valuecontact', self::DT_INTEGER);
    $this->valuegeneral = $this->AddField('valuegeneral', self::DT_INTEGER);
    $this->comment = $this->AddField('comment', self::DT_STRING);
    $this->commentstamp = $this->AddField('commentstamp', self::DT_DATETIME);
    $this->reply = $this->AddField('reply', self::DT_STRING);
    $this->replystamp = $this->AddField('replystamp', self::DT_DATETIME);
    $this->status = $this->AddField(self::FN_STATUS, self::DT_STATUS);
  }

  public function AssignFormFields($formeditor, $idref) {
    // business category section
/*    $formeditor->AssignActiveFieldSet(FS_OPENHOURS, 'Opening Hours');
    // - opening hours
    $monday = $formeditor->AddDataField($this, 'monday', 'Monday', FLDTYPE_EDITBOX, 50);
    $monday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $tuesday = $formeditor->AddDataField($this, 'tuesday', 'Tuesday', FLDTYPE_EDITBOX, 50);
    $tuesday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $wednesday = $formeditor->AddDataField($this, 'wednesday', 'Wednesday', FLDTYPE_EDITBOX, 50);
    $wednesday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $thursday = $formeditor->AddDataField($this, 'thursday', 'Thursday', FLDTYPE_EDITBOX, 50);
    $thursday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $friday = $formeditor->AddDataField($this, 'friday', 'Friday', FLDTYPE_EDITBOX, 50);
    $friday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $saturday = $formeditor->AddDataField($this, 'saturday', 'Saturday', FLDTYPE_EDITBOX, 50);
    $saturday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $sunday = $formeditor->AddDataField($this, 'sunday', 'Sunday', FLDTYPE_EDITBOX, 50);
    $sunday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
 */
  }

  protected function AssignDefaultFieldValues() {
    $this->AssignFieldDefaultValue(\dana\table\basetable::FN_STATUS, self::STATUS_ACTIVE, true);
  }

  public function ValidateFormFields($formeditor, $idref) {
    echo '<pre>ValidateFormFields</pre>' . CRNL;
  }

  public function ValidateFields() {
    echo '<pre>ValidateFields</pre>' . CRNL;
  }

  public function Show() {
    $ret = '(rating unavailable)';
    if ($this->exists) {
      $ret = '(rating available)';
    }
    return $ret;
  }

  public function FindByNickName($nickname) {
    $this->account = new account();
    return $this->AssignAccount(($this->account->FindByNickName($nickname)) ? $this->account : false);
  }

  public function FindByAccountID($accountid) {
    $this->account = new account($accountid);
    return $this->AssignAccount(($this->account->exists) ? $this->account : false);
  }

  private function BuildStatistics() {
    if (!self::$statistics) {
      $this->GetStatistics($this->account->ID());
    }
  }

  static private function AddToCommentList(
    &$commentlist, $visitorid, $visitorname, $comment, $commentstamp, $reply, $replystamp) {
    if (trim($comment)) {
      $commentlist[] = array(
        'VISITORID' => $visitorid,
        'VISITORNAME' => $visitorname,
        'COMMENT' => stripslashes($comment),
        'COMMENTSTAMP' => strtotime($commentstamp),
        'REPLY' => $reply,
        'REPLYSTAMP' => ($replystamp) ? strtotime($replystamp) : 0
      );
    }
  }

  protected function CalcOverall(
    $valuequality, $valuecost, $valuewaiting, $valueaftersale,
    $valueprofessional, $valuecontact, $valuegeneral) {
    $value = 0;
    $count = 0;
    if ($valuequality > self::RATE_NA) {
      $value += $valuequality;
      $count++;
    }
    if ($valuecost > self::RATE_NA) {
      $value += $valuecost;
      $count++;
    }
    if ($valuewaiting > self::RATE_NA) {
      $value += $valuewaiting;
      $count++;
    }
    if ($valueaftersale > self::RATE_NA) {
      $value += $valueaftersale;
      $count++;
    }
    if ($valueprofessional > self::RATE_NA) {
      $value += $valueprofessional;
      $count++;
    }
    if ($valuecontact > self::RATE_NA) {
      $value += $valuecontact;
      $count++;
    }
    if ($valuegeneral > self::RATE_NA) {
      $value += $valuegeneral;
      $count++;
    }
    return ($value + $count) ? ($value / $count) : 0;
  }

  static public function GetStatistics($accountid) {
    $totalquality = 0;
    $totalqualitycount = 0;
    $totalcost = 0;
    $totalcostcount = 0;
    $totalwaiting = 0;
    $totalwaitingcount = 0;
    $totalaftersale = 0;
    $totalaftersalecount = 0;
    $totalprofessional = 0;
    $totalprofessionalcount = 0;
    $totalcontact = 0;
    $totalcontactcount = 0;
    $totalgeneral = 0;
    $totalgeneralcount = 0;
    $count = 0;
    $commentlist = array();
    $query = 'SELECT * FROM `rating` ' .
      "WHERE `status` = '" . self::STATUS_ACTIVE . "' AND `accountid` = " . (int) $accountid .
      " ORDER BY `commentstamp` DESC";
    $result = \dana\core\database::Query($query);
    while ($line = $result->fetch_assoc()) {
      self::AddToCommentList(
        $commentlist,
        $line['visitorid'], $line['visitorname'], $line['comment'], 
        $line['commentstamp'], $line['reply'], $line['replystamp']
      );
      if ($line['valuequality'] > self::RATE_NA) {
        $totalquality += $line['valuequality'];
        $totalqualitycount += 1;
      }
      if ($line['valuecost'] > self::RATE_NA) {
        $totalcost += $line['valuecost'];
        $totalcostcount += 1;
      }
      if ($line['valuewaiting'] > self::RATE_NA) {
        $totalwaiting += $line['valuewaiting'];
        $totalwaitingcount += 1;
      }
      if ($line['valueaftersale'] > self::RATE_NA) {
        $totalaftersale += $line['valueaftersale'];
        $totalaftersalecount += 1;
      }
      if ($line['valueprofessional'] > self::RATE_NA) {
        $totalprofessional += $line['valueprofessional'];
        $totalprofessionalcount += 1;
      }
      if ($line['valuecontact'] > self::RATE_NA) {
        $totalcontact += $line['valuecontact'];
        $totalcontactcount += 1;
      }
      
      if ($line['valuegeneral'] > self::RATE_NA) {
        $totalgeneral += $line['valuegeneral'];
        $totalgeneralcount += 1;
      }
      $count++;
    }
    $result->free();
    $overallcount = ($totalqualitycount + $totalcostcount + $totalwaitingcount + 
      $totalaftersalecount + $totalprofessionalcount + $totalcontactcount + $totalgeneralcount);
    $overalltotal = ($totalquality + $totalcost + $totalwaiting + $totalaftersale + 
      $totalprofessional + $totalcontact + $totalgeneral);
    $overall = ($overalltotal) ? ($overalltotal / $overallcount) : 0;
    self::$statistics = array(
      self::RATINGTYPE_QUALITY => ($totalqualitycount) ? ($totalquality / $totalqualitycount) : 0,
      self::RATINGTYPE_QUALITY . 'count' => $totalqualitycount,
      self::RATINGTYPE_COST => ($totalcostcount) ? ($totalcost / $totalcostcount) : 0,
      self::RATINGTYPE_COST . 'count' => $totalcostcount,
      self::RATINGTYPE_WAITING => ($totalwaitingcount) ? ($totalwaiting / $totalwaitingcount) : 0,
      self::RATINGTYPE_WAITING . 'count' => $totalwaitingcount,
      self::RATINGTYPE_AFTERSALE => ($totalaftersalecount) ? ($totalaftersale / $totalaftersalecount) : 0,
      self::RATINGTYPE_AFTERSALE . 'count' => $totalaftersalecount,
      self::RATINGTYPE_PROFESSIONAL => ($totalprofessionalcount) ? ($totalprofessional / $totalprofessionalcount) : 0,
      self::RATINGTYPE_PROFESSIONAL . 'count' => $totalprofessionalcount,
      self::RATINGTYPE_CONTACT => ($totalcontactcount) ? ($totalcontact / $totalcontactcount) : 0,
      self::RATINGTYPE_CONTACT . 'count' => $totalcontactcount,
      self::RATINGTYPE_GENERAL => ($totalgeneralcount) ? ($totalgeneral / $totalgeneralcount) : 0,
      self::RATINGTYPE_GENERAL . 'count' => $totalgeneralcount,
      self::RATINGTYPE_OVERALL => $overall,
      self::RATINGTYPE_COUNT => $count,
      self::RATINGTYPE_COMMENTS => $commentlist
    );
  }

  private function AssignAccount($account) {
    self::$statistics = false;
    $this->rated = false;
    $this->account = $account;
    $this->accountexists = ($account) ? $this->account->exists : false;
    $accstatus = $this->account->GetCurrentStatus();
    $this->available =
      $this->accountexists && 
        (($accstatus == account::ACCSTATUS_PUBLISHED) || ($accstatus == account::ACCSTATUS_MODIFIED));
    if ($this->accountexists) {
      if ($this->available) {
        $businesname = $account->GetFieldValue('businessname');
        $this->title = "How good is &quot;{$businesname}&quot;?";
        $this->BuildStatistics();
        $this->rated = (self::$statistics) ? (self::$statistics[self::RATINGTYPE_OVERALL] > self::RATE_NA) : false;
      } else {
        $this->title = 'Currently Unavailable';
      }
    } else {
      $this->title = 'No Business Found';
    }
    return $this->accountexists;
  }

  public function ShowRating($ratetype, $title, $showratenow = false) {
    return $this->GetRatingStars($ratetype, $title, $showratenow);
  }

  public function ShowCommentsMade() {
    $this->BuildStatistics();
    $statistics = self::$statistics; //$this->GetRating(true);
    $commentlist = $statistics[self::RATINGTYPE_COMMENTS];
    $count = count($commentlist);
    if ($count) {
      $countmsg = CountToString($count, 'Comment', ''); // . ' Comment' . (($count > 1) ? 's' : '');
      $ret = array();
      $ret[] = "  <h3 class='title'>Found {$countmsg}</h3>";
      $ret[] = "  <ol class='accordionitem'>";
      foreach ($commentlist as $comment) {
        $visitorname = trim($comment['VISITORNAME']);
        if ($visitorname == '') {
          $visitorname = 'someone';
        }
        $when = date('jS M Y g:i a', $comment['COMMENTSTAMP']);
        $commentmsg = $comment['COMMENT'];
        $commentmsg = nl2br($commentmsg, false);
        $ret[] = "    <li>";
        $ret[] = "      <p class='commentheader'>{$visitorname}, wrote this on {$when}</p>";
        $ret[] = "      <blockquote class='commenttext'>&quot;{$commentmsg}&quot;</blockquote>";
        $ret[] = "    </li>";
      }
      $ret[] = "  </ol>";
      $ret = implode($ret, "\n");
    } else {
      $ret = '(no comments made)';
    }
    return $ret;
  }

  public function ShowVistorName($title) {
    $ctrl = 'fldvisitorname';
    $ret = array();
    $ret[] = "<div class='ratingquestions'>";
    $ret[] = "  <h3>{$title}</h3>";
    $ret[] = "  <div class='ratingquestion'>";
    $ret[] = "    <input type='text' value='' placeholder='e.g. john smith' maxlength='50' style='width: 520px; float: none;' size='30' name='{$ctrl}' id='{$ctrl}' />";
    $ret[] = "  </div>";
    $ret[] = "</div>";
    return implode("\n", $ret);
  }

  public function ShowRatingQuestion($ratetype, $title) {
    $ctrl = 'fldvalue' . $ratetype;
    $ret = array();
    $ret[] = "<div class='ratingquestions'>";
    $ret[] = "  <h3>{$title}</h3>";
    $ret[] = "  <div class='ratingquestion'>";
    $ret[] = "    <label for='{$ctrl}5'>Excellent</label>";
    $ret[] = "    <input type='radio' name='{$ctrl}' id='{$ctrl}5' value='5' />";
    $ret[] = "  </div>";
    $ret[] = "  <div class='ratingquestion'>";
    $ret[] = "    <label for='{$ctrl}4'>Very Good</label>";
    $ret[] = "    <input type='radio' name='{$ctrl}' id='{$ctrl}4' value='4' />";
    $ret[] = "  </div>";
    $ret[] = "  <div class='ratingquestion'>";
    $ret[] = "    <label for='{$ctrl}3'>Good</label>";
    $ret[] = "    <input type='radio' name='{$ctrl}' id='{$ctrl}3' value='3' />";
    $ret[] = "  </div>";
    $ret[] = "  <div class='ratingquestion'>";
    $ret[] = "    <label for='{$ctrl}2'>Poor</label>";
    $ret[] = "    <input type='radio' name='{$ctrl}' id='{$ctrl}2' value='2' />";
    $ret[] = "  </div>";
    $ret[] = "  <div class='ratingquestion'>";
    $ret[] = "    <label for='{$ctrl}1'>Bad</label>";
    $ret[] = "    <input type='radio' name='{$ctrl}' id='{$ctrl}1' value='1' />";
    $ret[] = "  </div>";
    $ret[] = "</div>";
    return implode("\n", $ret);
  }

  public function ShowRatingComment($title) {
    $ctrl = 'fldcomment';
    $ret = array();
    $ret[] = "<div class='ratingquestions'>";
    $ret[] = "  <h3>{$title}</h3>";
    $ret[] = "  <div class='ratingquestion'>";
    $ret[] = "    <textarea name='{$ctrl}' id='{$ctrl}' cols='70' rows='10'></textarea>";
    $ret[] = "  </div>";
    $ret[] = "</div>";
    return implode("\n", $ret);
  }

  public function ShowSponsorDetails() {
    $ret = array();
    $ret[] = "<table id='sponsorgrid' cellpadding='5'>";
    $ret[] = "  <thead>\n";
    $ret[] = "    <tr>";
    $ret[] = "      <th class='dateheading'>Start Date</th><th class='dateheading'>End Date</th><th>Description</th><th>Amount</th><th>Status</th>";
    $ret[] = "    </tr>";
    $ret[] = "  </thead>\n";
    $ret[] = "  <tfoot>\n";
    $ret[] = "    <tr>\n";
    $ret[] = "      <th></th><th></th><th></th><th></th><th></th>";
	  $ret[] = "    </tr>\n";
    $ret[] = "  </tfoot>\n";
    $ret[] = "  <tbody>\n";
    foreach($this->sponsorrows as $id => $line) {
      $amount = $line['amount'];
      $startdate = $line['startdate'];
      $enddate = $line['enddate'];
      $desc = $line['description'];
      $status = $line['status'];
      $class = ($status == self::STATUS_ACTIVE) ? " class='activerow'" : '';
      $ret[] = "  <tr{$class}>";
      $ret[] = "    <td>{$startdate}</td>";
      $ret[] = "    <td>{$enddate}</td>";
      $ret[] = "    <td>{$desc}</td>";
      $ret[] = "    <td style='text-align:right;'>{$amount}</td>";
      $ret[] = "    <td>{$status}</td>";
      $ret[] = "  </tr>";
    }
    $ret[] = "  </tbody>\n";
    $ret[] = "</table>";
    return implode("\n", $ret);
  }
  
  static public function ShowSponsorForm() {
    $session = isset($_COOKIE['sid']) ? $_COOKIE['sid'] : false;
    $details = ($session) ? sponsor::FindAccountAndContact($session) : false;
    if ($details) {
      $contact = $details['contcatname'];
      $businessname = $details['businessname'];
      $ret = array();
      $ret[] = '<div name="sponsordetails" id="sponsordetails">';
      $ret[] = '  <h2>Book a place on our Spotlight now!</h2>';
      $ret[] = '  <p><strong>Only &pound;7 per week</strong> &mdash; for a limit time - normally &pound;17.50</p>';
      $ret[] = '  <h3>Your details</h3>';
      $ret[] = "  <p>Your name: <strong>{$contact}</strong></p>";
      $ret[] = "  <p>Business: <strong>{$businessname}</strong></p>";
      $ret[] = '  <input class="redbutton" type="button" id="btnlogout" value="logout" />';
      $ret[] = '  <div id="sponsorconfirm"></div>';
      $ret[] = sponsor::BuildStartDateList('sponsorstartdate'); // show the start date
      $ret[] = '  <div id="sponsorselection"></div>';
      $ret[] = '  <div id="sponsoradvert"></div>';
      $ret[] = '  <script>';
      $ret[] = '    $(function() {';
      $ret[] = '      $("#btnlogout").click(function(event) {';

      $ret[] = '$.removeCookie("uid");';
      $ret[] = '$.removeCookie("sid");';
      $ret[] = '            window.location.reload(true);';
//      $ret[] = '        $.ajax({';
//      $ret[] = '          url: "scripts/ajax.sponsorselection.php?a=l",'; // logout
//      $ret[] = '          success: function(data) {';
//      $ret[] = '            window.location.reload(true);';
//      $ret[] = '          }';
//      $ret[] = '        });';
      $ret[] = '      });';
      
      $ret[] = '      $("#startdate").click(function(event) {';
      $ret[] = '        $("#sponsorselection").html("<p>Please wait...</p>");';
      $ret[] = '        $.ajax({';
      $ret[] = '          url: "scripts/ajax.sponsorselection.php?a=s&s=' . $session . '",'; // process the start date
      $ret[] = '          type: "POST",';
      $ret[] = '          data: {';
      $ret[] = '            startdate: $("#sponsorstartdate option:selected").val()';
      $ret[] = '          },';
      $ret[] = '          success: function(data) {';
      $ret[] = '            $("#sponsorselection").html(data);';
      $ret[] = '          }';
      $ret[] = '        });';
      $ret[] = '      });';
      $ret[] = '    });';
      $ret[] = '  </script>';
      $ret[] = '</div>';
      echo implode("\n", $ret);
    } else {
      echo "<p class='error'>Sorry, there was a problem. Please try again later.</p>\n";
    }
  }

  static private function GetStatLine($value, $text = '') {
    if ($value > self::RATE_NA) {
      switch ($value) {
        case self::RATE_EXCELLENT:
          $rate = 'EXCELLENT'; break;
        case self::RATE_VERYGOOD:
          $rate = 'Very Good'; break;
        case self::RATE_GOOD:
          $rate = 'Good'; break;
        case self::RATE_POOR:
          $rate = 'Poor'; break;
        case self::RATE_BAD:
          $rate = 'Bad'; break;
        default:
          $rate = 'n/a'; break;
      }
      if ($text) {
        $ret = "{$text} was {$rate}";
      } else {
        $ret = $rate;
      }
    } else {
      $ret = false;
    }
    return $ret;
  }

  static private function BuildStars($count, $title, $msg) {
    $titlemsg = ($title) ? "        <li class='ratingcaption'>{$title}</li>\n" : '';
    $ret =
      "      <div class='ratingoverall'>\n" .
      "        <ul>\n" .
      $titlemsg;
    for($lp=1; $lp <= $count; $lp++) {
      $ret .=
      "          <li class='star'>&nbsp;</li>\n";
    }
    $ret .=
      "          <li class='ratingcaption'>{$msg}</li>\n" .
      "        </ul>\n" .
      "        <div class='clear'>&nbsp;</div>\n" .
      "      </div>\n";
    return $ret;
  }

  static private function GetPlural($value, $text) {
    return ($value == 0) 
      ? 'zero'
      : (($value > 1) 
        ? $text . 's'
        : $text);
  }

  static public function GetRatingStars($ratingtype, $title = '', $showratenow = false) {
    $statistics = self::$statistics;
    $rating = $statistics[$ratingtype];
    if (is_numeric($rating)) {
      $rating = floor($rating);
    }
    $showratenowbtn = ($ratingtype == self::RATINGTYPE_OVERALL) && $showratenow;
    if ($rating > 0) {
      $count = $statistics[self::RATINGTYPE_COUNT];
      $countmsg = self::GetPlural($count, $count . ' rating');
      if ($showratenowbtn) {
        $commentcount = count($statistics[self::RATINGTYPE_COMMENTS]);
        if ($commentcount) {
          $countmsg .= self::GetPlural($commentcount, " ({$commentcount} comment"). ')';
        }
        $ratenowbtn = "<a href='rate.php?id=" . account::$instance->ID() . "' title='click to view rating of this business'>{$countmsg}</a>";
      } else {
        $count = $statistics[$ratingtype . 'count'];
        if ($count) {
          //$ratedesc = '<strong>' . self::GetStatLine($rating) . '</strong>';
          //$ratenowbtn = "{$ratedesc} ({$count} ratings)";
          $ratenowbtn = '<small>(' . self::GetPlural($count, $count . ' time') . ')</small>';
        }
      }
      $ret = self::BuildStars($rating, $title, $ratenowbtn);
    } elseif ($ratingtype == self::RATINGTYPE_OVERALL) {
      $ratenowbtn = "<a href='rate.php?id=" . account::$instance->ID() . "' title='click to rate this business'>Rate</a>";
      $ret = "<p class='ratingcaption clear'>(not rated)&nbsp;{$ratenowbtn}</p>\n";
    } else {
      $ret = '';
    }
    return $ret;
  }

  private function FormatMessage($message) {
     $ret = '';
     foreach ($message as $line) {
       if ($line) {
         $ln = str_replace("\n", ' ', $line);
         $ret .= wordwrap($ln, 70) . "\n";
       }
     }
     return $ret;
  }

  private function ProceesStats() {
    $overallcount = 0;
    $overalltotal = 0;
    $valuequality = $this->GetFieldValue('value' . self::RATINGTYPE_QUALITY);
    if ($valuequality) {
      $overallcount++;
      $overalltotal += $valuequality;
    }
    $valuecost = $this->GetFieldValue('value' . self::RATINGTYPE_COST);
    if ($valuecost) {
      $overallcount++;
      $overalltotal += $valuecost;
    }
    $valuewaiting = $this->GetFieldValue('value' . self::RATINGTYPE_WAITING);
    if ($valuewaiting) {
      $overallcount++;
      $overalltotal += $valuewaiting;
    }
    $valueaftersale = $this->GetFieldValue('value' . self::RATINGTYPE_AFTERSALE);
    if ($valueaftersale) {
      $overallcount++;
      $overalltotal += $valueaftersale;
    }
    $valueprofessional = $this->GetFieldValue('value' . self::RATINGTYPE_PROFESSIONAL);
    if ($valueprofessional) {
      $overallcount++;
      $overalltotal += $valueprofessional;
    }
    $valuecontact = $this->GetFieldValue('value' . self::RATINGTYPE_CONTACT);
    if ($valuecontact) {
      $overallcount++;
      $overalltotal += $valuecontact;
    }
    $valuegeneral = $this->GetFieldValue('value' . self::RATINGTYPE_GENERAL);
    if ($valuegeneral) {
      $overallcount++;
      $overalltotal += $valuegeneral;
    }
    $overall = ($overallcount) ? $overalltotal / $overallcount : 0;
    $ratingquality = $this->GetStatLine($valuequality, 'Quality of service / products');
    $ratingcost = $this->GetStatLine($valuecost, 'Cost of the service / products');
    $ratingwaiting = $this->GetStatLine($valuewaiting, 'Customer service');
    $ratingaftersale = $this->GetStatLine($valueaftersale, 'Service after payment was made');
    $ratingprofessional = $this->GetStatLine($valueprofessional, 'Professionalism of the business');
    $ratingcontact = $this->GetStatLine($valuecontact, 'Email/telephone response');
    $ratinggeneral = $this->GetStatLine($valuegeneral, 'General experience');
    $ratingoverall = number_format($overall, 1);
    $ratingcomment = stripslashes($this->GetFieldValue('comment'));
    if ($ratingoverall > 0) {
      $this->SetFieldValue('value' . self::RATINGTYPE_OVERALL, floor($ratingoverall));
      $ret = parent::StoreChanges();
      if ($ret > 0) {
        require_once 'class.table.history.php';
        //$accountid = $this->account->ID();
        if (!$this->account) {
          $this->account = new account($this->accountid);
        }
        $businessname = $this->account->GetFieldValue('businessname');
        $details = "accountid: {$accountid}, business: {$businessname}";
        history::MakeHistoryItem(
          $accountid, HISTORY_NEWRATING, $details
        );
        require_once 'class.table.emailhistory.php';
        $visitorname = $this->GetFieldValue('visitorname');
        if (!$visitorname) {
          $visitorname = '(name not given)';
        }
        if ($ratingcomment) {
          $comment = wordwrap(stripslashes($ratingcomment), 70); //$this->GetFieldValue('comment'));
        } else {
          $comment = '(no comment made)';
        }
        $ratings = array(
          'Rating Details:',
          $ratingquality, $ratingcost, $ratingwaiting, $ratingaftersale,
          $ratingprofessional, $ratingcontact, $ratinggeneral, ' ',
          'OVERALL RATING: ' . $ratingoverall, ' ',
          'Comment: ' . $comment
        );
        // send message to US
        $message = array();      
        $message[] = 'A business has been rated:';
        $message[] = "Business:{$businessname} ({$accountid})";
        $message[] = ' ';
        $message[] = "Name: {$visitorname}";
        $message[] = ' ';
        $message = array_merge($message, $ratings);
        $msg = $this->FormatMessage($message);
        emailhistory::SendSystemEmailMessage(
          emailhistory::ET_RATING, 'Business Rated', $msg, $accountid);
        // send message to ACCOUNT HOLDER
        if ($this->account->Contact()->GetFieldValue('notifyrating')) {
          $firstname = $this->account->Contact()->GetFieldValue('firstname');
          $message = array();
          $message[] = "Hi {$firstname},";
          $message[] = "Your business, {$businessname}, was just rated.";
          $message[] = ' ';
          $message[] = "Name: {$visitorname}";
          $message = array_merge($message, $ratings);
          $message[] = 'Please note that we cannot be held responsible for the rating and comments';
          $message[] = 'PLEASE DO NOT REPLY TO THIS MESSAGE';
          $message[] = ' ';
          $msg = $this->FormatMessage($message);
          emailhistory::SendEmailMessage(
            emailhistory::ET_RATING, $this->account->Contact()->GetFieldValue('email'),
            'Your Business Was Rated', $msg, EMAIL_SUPPORT, $accountid);
        }
      }
    } else {
      $ret = false;
    }
    return $ret;
  }
  
  public function StoreChanges() {
    if ($this->dostats) {
      $ret = $this->ProceesStats();
    } else {
      $ret = parent::StoreChanges();
    }
    return $ret;
  }

  public function GetPostedFields($prefix = 'fld') {
    parent::GetPostedFields();
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARTDED_FOR'] != '') {
      $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ipaddress = $_SERVER['REMOTE_ADDR'];
    }
    $this->SetFieldValue('ipaddress', $ipaddress);
    $this->SetFieldValue('accountid', $this->account->ID());
  }

  public function AssignDataGridColumns($datagrid) {
    $datagrid->showactions = false;
    $datagrid->AddColumn('DESC', 'Visitor', false, 'important');
    $datagrid->AddColumn('DATE', 'Date');
    $datagrid->AddColumn('VALUEOVERALL', 'Overall', false, 'right important');
    $datagrid->AddColumn('HASCOMMENT', 'Comment');
    $datagrid->AddColumn('REPLIED', 'Replied', true);
  }

  public function AssignDataGridRows($datagrid) {
    $accountid = account::$instance->ID();
    $status = self::STATUS_ACTIVE;
    $query =
      'SELECT * FROM `rating` ' .
      "WHERE (`accountid` = {$accountid}) AND " .
      "(`status` = '{$status}') " .
      'ORDER BY `commentstamp` DESC';
    $list = array();
    $result = \dana\core\database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $overall = $this->CalcOverall(
        $line['valuequality'], $line['valuecost'], $line['valuewaiting'], $line['valueaftersale'],
        $line['valueprofessional'], $line['valuecontact'], $line['valuegeneral']);
      $coldata = array(
        'DESC' => $line['visitorname'],
        'DATE' => date('j-M-Y', strtotime($line['commentstamp'])),
        'VALUEOVERALL' => $overall,
        'HASCOMMENT' => ($line['comment']) ? 'YES' : 'no',
        'REPLIED' => ($line['reply']) ? 'YES' : (($line['comment']) ? 'no (reply now?)' : '')
      );
      $datagrid->AddRow($id, $coldata, true, array());
    }
    $result->free();
    return $list;
  }
}
