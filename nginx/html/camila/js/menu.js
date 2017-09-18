/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2008 Umberto Bresciani

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


function in_array(needle, haystack)
{
for (h in haystack) {
if (haystack[h] == needle) {
return true;
}
}
return false;
}

function find_in_array(needle, haystack)
{
for (h in haystack) {
if (haystack[h] == needle) {
return h;
}
}
return false;
}


function print_children(parents,titles,visible,urls,father)
 {
   var counter=0;
   var h = new Hash();

    
   for(var i=0;i<parents.length;i++) {

	   
	   if(parents[i] == father) {

         counter++;
         
         var subhash = print_children(parents,titles,visible,urls,urls[i]);

         var hash;
           
         if (in_array(urls[i],parents))
             hash = new Hash(
              'contents', titles[i],
              'uri', '',
              'statusText', '...'
         )
         else {
	        var purl = urls[i];
	        var parent_url='';
	        while ( purl != '') {
		          parent_url = purl;
		          purl = parents[find_in_array(purl,urls)];
		    }
		    
		    var parent_index = find_in_array(parent_url,urls);
		    var title = titles[parent_index];

            hash = new Hash(
              'contents', titles[i],
              //'uri', 'javascript:newWindow(\''+title+'\',\''+title+'\',\'window.php?'+urls[i]+'\',\'#ffffff\')',
              'uri', urls[i],
              'statusText', '...'
             )
         }

            for (var j = 0; j < subhash.size(); j++) {
	            hash.set(j+1, subhash.get(j+1))
	        }

            h.set(counter, hash);

       }
   }

         return h;
 }
