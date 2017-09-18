<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2009 Umberto Bresciani

   Camila PHP Framework is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Camila PHP Framework is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Camila PHP Framework; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA */


  function camila_date_add($interval, $number, $date)
  {
      $date_time_array = getdate($date);
      $hours = $date_time_array['hours'];
      $minutes = $date_time_array['minutes'];
      $seconds = $date_time_array['seconds'];
      $month = $date_time_array['mon'];
      $day = $date_time_array['mday'];
      $year = $date_time_array['year'];
      
      switch ($interval) {
          case 'yyyy':
              $year += $number;
              break;
          case 'q':
              $year += ($number * 3);
              break;
          case 'm':
              $month += $number;
              break;
          case 'y':
          case 'd':
          case 'w':
              $day += $number;
              break;
          case 'ww':
              $day += ($number * 7);
              break;
          case 'h':
              $hours += $number;
              break;
          case 'n':
              $minutes += $number;
              break;
          case 's':
              $seconds += $number;
              break;
      }
      $timestamp = mktime($hours, $minutes, $seconds, $month, $day, $year);
      return $timestamp;
  }


  function camila_date_diff($interval, $datefrom, $dateto, $using_timestamps = false)
  {
      /*
       $interval can be:
       yyyy - Number of full years
       q - Number of full quarters
       m - Number of full months
       y - Difference between day numbers
       (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
       d - Number of full days
       w - Number of full weekdays
       ww - Number of full weeks
       h - Number of full hours
       n - Number of full minutes
       s - Number of full seconds (default)
       */
      
      if (!$using_timestamps) {
          $datefrom = strtotime($datefrom, 0);
          $dateto = strtotime($dateto, 0);
      }
      // Difference in seconds
      $difference = $dateto - $datefrom;
      
      switch ($interval) {
          case 'yyyy':
              // Number of full years
              
              $years_difference = floor($difference / 31536000);
              if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom) + $years_difference) > $dateto) {
                  $years_difference--;
              }
              if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto) - ($years_difference + 1)) > $datefrom) {
                  $years_difference++;
              }
              $datediff = $years_difference;
              break;
              
          case "q":
              // Number of full quarters
              
              $quarters_difference = floor($difference / 8035200);
              while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($quarters_difference * 3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                  $months_difference++;
              }
              $quarters_difference--;
              $datediff = $quarters_difference;
              break;
              
          case "m":
              // Number of full months
              
              $months_difference = floor($difference / 2678400);
              while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                  $months_difference++;
              }
              $months_difference--;
              $datediff = $months_difference;
              break;
              
          case 'y':
              // Difference between day numbers
              
              $datediff = date("z", $dateto) - date("z", $datefrom);
              break;
              
          case "d":
              // Number of full days
              
              $datediff = floor($difference / 86400);
              break;
              
          case "w":
              // Number of full weekdays
              
              $days_difference = floor($difference / 86400);
              // Complete weeks
              $weeks_difference = floor($days_difference / 7);
              $first_day = date("w", $datefrom);
              $days_remainder = floor($days_difference % 7);
              // Do we have a Saturday or Sunday in the remainder?
              $odd_days = $first_day + $days_remainder;
              if ($odd_days > 7) {
                  // Sunday
                  $days_remainder--;
              }
              if ($odd_days > 6) {
                  // Saturday
                  $days_remainder--;
              }
              $datediff = ($weeks_difference * 5) + $days_remainder;
              break;
              
          case "ww":
              // Number of full weeks
              
              $datediff = floor($difference / 604800);
              break;
              
          case "h":
              // Number of full hours
              
              $datediff = floor($difference / 3600);
              break;
              
          case "n":
              // Number of full minutes
              
              $datediff = floor($difference / 60);
              break;
              
          default:
              // Number of full seconds (default)
              
              $datediff = $difference;
              break;
      }
      
      return $datediff;
  }
/*
 * camila_days_in_month($month, $year)
 * Returns the number of days in a given month and year, taking into account leap years.
 *
 * $month: numeric month (integers 1-12)
 * $year: numeric year (any integer)
 */
function camila_days_in_month($month, $year)
{
    return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
}


function camila_get_weeks_old($date)
{
  $day = date('d', $date);
  $month = date('m', $date);
  $year = date('Y', $date);

  // Get the first day of the month
  $month_start = mktime(0,0,0,$month, 1, $year);

  // Get friendly month name
  $month_name = date('M', $month_start);

  // Figure out which day of the week
  // the month starts on.
  $month_start_day = date('D', $month_start);

  switch($month_start_day){
      case "Sun": $offset = 0; break;
      case "Mon": $offset = 1; break;
      case "Tue": $offset = 2; break;
      case "Wed": $offset = 3; break;
      case "Thu": $offset = 4; break;
      case "Fri": $offset = 5; break;
      case "Sat": $offset = 6; break;
  }

  // determine how many days are in the last month.
  if($month == 1){
     $num_days_last = camila_days_in_month(12, ($year -1));
  } else {
     $num_days_last = camila_days_in_month(($month -1), $year);
  }
  // determine how many days are in the current month.
  $num_days_current = camila_days_in_month($month, $year);

  // Build an array for the current days
  // in the month
  for($i = 1; $i <= $num_days_current; $i++){
      $num_days_array[] = $i;
  }

  // Build an array for the number of days
  // in last month
  for($i = 1; $i <= $num_days_last; $i++){
      $num_days_last_array[] = $i;
  }

  // If the $offset from the starting day of the
  // week happens to be Sunday, $offset would be 0,
  // so don't need an offset correction.

  if($offset > 0){
      $offset_correction = array_slice($num_days_last_array, -$offset, $offset);
      $new_count = array_merge($offset_correction, $num_days_array);
      $offset_count = count($offset_correction);
  }

  // The else statement is to prevent building the $offset array.
  else {
      $offset_count = 0;
      $new_count = $num_days_array;
  }

  // count how many days we have with the two
  // previous arrays merged together
  $current_num = count($new_count);

  // Since we will have 5 HTML table rows (TR)
  // with 7 table data entries (TD)
  // we need to fill in 35 TDs
  // so, we will have to figure out
  // how many days to appened to the end
  // of the final array to make it 35 days.


  if($current_num > 35){
     $num_weeks = 6;
     $outset = (42 - $current_num);
  } elseif($current_num < 35){
     $num_weeks = 5;
     $outset = (35 - $current_num);
  }
  if($current_num == 35){
     $num_weeks = 5;
     $outset = 0;
  }
  // Outset Correction
  for($i = 1; $i <= $outset; $i++){
     $new_count[] = $i;
  }

  // Now let's "chunk" the $all_days array
  // into weeks. Each week has 7 days
  // so we will array_chunk it into 7 days.
  $weeks = array_chunk($new_count, 7);

  return $weeks;

}

//function generate_calendar($year, $month, $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0, $pn = array()){
function camila_get_weeks($date, $first_day = 1) {
    $year = date('Y', $date);
    $month = date('n', $date);

    $prev_month = $month - 1;
    $prev_year = $year;
    if ($prev_month == 0) {
        $prev_year = $year-1;
        $prev_month = 12;
    }

    $first_of_month = gmmktime(0,0,0,$month,1,$year);

    #remember that mktime will automatically correct if invalid dates are entered
    # for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
    # this provides a built in "rounding" feature to generate_calendar()

    $day_names = array(); #generate all the day names according to the current locale
    for($n=0,$t=(3+$first_day)*86400; $n<7; $n++,$t+=86400) #January 4, 1970 was a Sunday
        $day_names[$n] = ucfirst(gmstrftime('%A',$t)); #%A means full textual day name

    list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month));
    $weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day

    $cal = Array();
    $row = Array();
    $rowcount = 0;
    if ($weekday > 0) {
        $prevlast = camila_days_in_month($prev_month, $prev_year);
        for ($i=0; $i<$weekday; $i++)
            $row[$i] = $prevlast - $weekday + $i +1;
    }

    for($day=1,$days_in_month=gmdate('t',$first_of_month); $day<=$days_in_month; $day++,$weekday++){
        if($weekday == 7){
            $cal[$rowcount] = $row;
            $rowcount++;
            $row = Array();
            $weekday   = 0; #start a new week
            $calendar .= "</tr>\n<tr>";
        }

            $row[]=$day;
            $calendar .= "<td>$day</td>";
    }
    if($weekday != 7) {
        for ($i=0; $i<(7-$weekday); $i++)
            $row[]=$i+1;
     }

            $cal[$rowcount] = $row;

    return $cal;
    //return $calendar."</tr>\n</table>\n";
}
//echo generate_calendar(2010, 12, 16,3,NULL,0,15, $first_of_month, $day_names, $day_names[$n]);
//#echo generate_calendar($year, $month, $days,$day_name_length,$month_href,$first_day,$pn);

?>
