function check_integer ( n ) { return RegExp ( "^[-+]?[0-9]+$" ).test( n ); }
function check_string ( s ) { return RegExp ( "^[a-zA-Z]+$" ).test( s ); }
function check_alfanum_string ( s ) { return RegExp ( "^[a-zA-Z0-9]+$" ).test( s ); } 
function check_date ( s ) { return RegExp ( "^[0-9]{4,4}.[0-9]{2,2}.[0-9]{2,2}$" ).test( s ); }
function check_time ( s ) { return RegExp ( "^[012][0-9]:[0-5][0-9]$" ).test( s ); }
function check_email ( s ) { return RegExp ( "^[a-zA-Z0-9-_.]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,4}$" ).test( s ); }
function check_float ( n )
{
	if (n.length == 0) return false;

	var first_char = n.charAt(0);
	if (first_char != '-' && first_char != '.' &&
	    (first_char < '0' || first_char > '9')) return false;

	var dot = false;
	var digit_expected = false;
	if (first_char == '.') {
		dot = true;
		digit_expected = true;
	} else if (first_char == '-')
		digit_expected = true;

	if (digit_expected && n.length < 2) return false;

	for (var count = 1; count < n.length; count++) {
		var c = n.charAt(count);
		if (c == '.') {
			if (dot) return false;
			dot = true;
		} else if (c < '0' || c > '9')
			return false;
	}

	return true;
}
