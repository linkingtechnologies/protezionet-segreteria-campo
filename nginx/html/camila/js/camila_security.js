/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2011 Umberto Bresciani

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



function randstr(arg)
{
    var str = '';
    var seed = Math.floor(Math.random()*arg.length);
        str = arg[seed];
    return str;
}

function camila_generate_password(pwd_len,type)
{
    var count = new Date().getSeconds();
    for (c=0; c<count; c++)
        Math.random();
    var cons_lo = ['b','c','d','f','g','h','j','k','l','m','n','p','q','r','s','t','v','w','x','y','z'];
    var cons_up = ['B','C','D','F','G','H','J','K','L','M','N','P','Q','R','S','T','V','W','X','Y','Z'];
    var hard_cons_lo = ['b','c','d','f','g','h','k','m','p','s','t','v','z'];
    var hard_cons_up = ['B','C','D','F','G','H','K','M','P','S','T','V','Z'];
    var link_cons_lo = ['h','l','r'];
    var link_cons_up = ['H','L','R'];
    var vowels_lo = ['a','e','i','o','u'];
    var vowels_up = ['A','E','I','U'];
    var digits = ['1','2','3','4','5','6','7','8','9'];

    if (type == 'numeric')
        var names = digits;
    else
        var names = [cons_lo, cons_up, digits, hard_cons_lo, hard_cons_up, digits, link_cons_lo, link_cons_up, digits, vowels_lo, vowels_up, digits];

    var newpass= '';
    for(i=0; i<pwd_len; i++)
        newpass = newpass + randstr(names[Math.floor(Math.random()*names.length)]);
    return newpass;
}