## php-sqlite-odata
Publish a sqlite database as an OData REST-service.

## Usage
Copy alongside a sqlite database and edit Config.class.php

## Limitations
* Only single tables with one (integer) primary key
* Table name = collection name
* CRUD (Create, Read, Update, Delete)
* Edm Datatypes
  * Int32, String, Boolean

## Roadmap
* Navigation properties based on foreign keys
* Use sqlite rowid as primary key
* Additional datatypes

## Dependencies
Using (modified) [AltoRouter](https://github.com/slup/AltoRouter) for routing

## License
The MIT License (MIT)

Copyright (c) 2015 Werner Schwarz, Patrick Wenger

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
