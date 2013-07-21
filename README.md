# Bacalaureat data

This repository contains the [Romanian Baccalaureate](http://en.wikipedia.org/wiki/Romanian_Baccalaureate) results and scraper scripts, which are published on the Internet bi-annually, but are not made available in downloadable and accessible formats, only via badly obfuscated HTML pages. Regardless, this information is in public domain, so I'm not sure why they went through all the trouble to make the data "hard" to scrape.

The scraper scripts are written in PHP (which was a massive mistake performance-wise) and require at least v5.4 to run.

## 2012 data

### Pre-processed data

Look for the files matching the pattern `12-[07|09].[csv|js|xml].rar` in this repository for readily downloadable pre-processed data. These files were created using `parse12.php`. The `07` is the July session, while `09` is the September session. The two are not merged, meaning people who passed in `12-09` are still with the failing grade in `12-07`.

For more information on how they're generated, read the DIY sections below and possibly consult the source.

### Do-it-yourself

#### gen12.php

Generates a list of URLs of the results in two separate files, `12-07.lst` and `12-09.lst`. Download these links to `data12` via a download manager. You can use `wget -i 12-XX.lst -P data12/` to grab them all.

This will download 29964 files totaling 2 GB in size. Will take approximately 10 minutes on a 100 Mb/s connection.

The files you're supposed to download are already available in a nicely compressed archive located at `data12/data12.rar`.

#### a3bacalaureat.as

This file is NOT part of the data processing, so you may skip this section, if you're not curious how incompetent the developers of bacalaureat.edu.ro are.

Apparently they don't like people scraping their data (which is in public domain) so they've implemented some anti-scraping measures. And really pathetic ones at that.

Each page is a static HTML, but it does not contain the information in plain-text, instead, it has a `<script>` section with a function called `ged()` which returns a base64-encoded string. However, upon trying to decode this, you'll only find yourself with garbage data. What happens next is that a Flash file is loaded, `a3bacalaureat.swf` to be exact, on which the `s3()` function is invoked. Twice, in fact, in nested `try/catch`. (At this point, you're kind-of [getting to know the programmer](http://en.wikipedia.org/wiki/Code_smell) you're up against. But let's keep the faith, and think that the programmer was just close to the deadline and was encountering a [Heisenbug](http://en.wikipedia.org/wiki/Heisenbug) on some page loads. The *obvious* solution is *obviously* to [retry on error](http://en.wikipedia.org/wiki/Coding_by_exception) once again then [fuck it](http://en.wikipedia.org/wiki/Error_hiding) if that failed too.) So, I downloaded the SWF file, thinking that I'll probably just have to resort to using some web browser automation (such as [Selenium](https://code.google.com/p/selenium/) or [PhantomJS](http://phantomjs.org/)) to run the Flash and extract the rendered HTML. However, a simple Google search for "flash decompiler" led me to an online service, where I uploaded the aforementioned file, expecting an error saying that the SWF is probably obfuscated and protected against disassembly. Boy, was I disappointed when the source of `s3()` greeted me. And then I got even more disappointed when I looked at it. It just retrieves the string from `ged()` then runs a bunch of character replaces, then some more character replaces switching between their cases, then just decoding that string as base64 and returning it to `sdd()`, a JavaScript function which then `document.write()`-s it.

The SWF file also contains `com.dynamicflash.util.Base64`, which is licensed under the MIT license, requiring them to distribute a copy of the license and the copyright notice of the library's developer with each copy of their [security through obscurity](http://en.wikipedia.org/wiki/Security_through_obscurity) contraption. Of course, they *forgot* to do so.

The `a3bacalaureat.as` file contains the full source code of `a3bacalaureat.swf`. Read and weep. The PHP implementation is located at the bottom of `parse12.php`.

#### parse12.php

Parses the pages located in `data12/` into three open formats: CSV, JSON and XML. It is recommended to run the whole thing on an SSD, otherwise it's going to take a while, since each student object is flushed to disk immediately after creation in order to avoid memory exhaustion issues.

This script will be significantly slower than `parse13.php`, because it has to *decrypt* the data before processing it. For more information, read the section above, titled `a3bacalaureat.as`.

This script will create six files in `YY-MM.FMT` format. Expect each file to be 20-140 MB in size.

## 2013 data

### Pre-processed data

Look for the files matching the pattern `13-07.[csv|js|xml].rar` in this repository for readily downloadable pre-processed data. These files were created using `parse13.php`. At the time of this commit it wasn't/isn't September (yet), therefore the `09` data is not yet available.

I will try to update the scripts and include the pre-processed data, but since this project isn't a priority, expect up to ~1 month of delay.

### Do-it-yourself

#### get13.php

The 2013 Bacalaureat site is not just a bunch of static pages, and because of this, it needs a proper crawler. `get13.php` is different from `gen12.php` in the sense that it downloads the files, not just generates a list of URLs. Make sure to create `data13/` and that it's empty. The `__VIEWSTATE` and `__EVENTVALIDATION` fields update with every pagination, and it is not possible to resume an interrupted session. You will need to update these fields in the script from the very last downloaded page in order to do so. These tokens may also expire (I'm not sure), so if the ones in the script currently don't work, just go to the [results page](http://bacalaureat.edu.ro/Pages/TaraRezultAlfa.aspx) and extract the values of `<input type="hidden" name="__[VIEWSTATE|EVENTVALIDATION]" value="..." />` from the source of the page.

This script will take about 12 hours to run, which will give you 18885 files totaling 9.1 GB in size.

If you don't feel like waiting 12 hours, the data is available for download at `data13/data13.rar`. The data in this archive is stripped, taking up only 4.5 GB. The size of the archive is only 14 MB. Upon decompression, look at the size of `data13` and [think of this meme](http://i.imgur.com/PDnFE.jpg).

#### strip13.php

This file is NOT part of the data processing, so you may skip this section, if you're not curious how incompetent the developers of bacalaureat.edu.ro are.

This script removes the `__VIEWSTATE` and `__EVENTVALIDATION` values from the files downloaded to `data13`. These fields take up 250 kB of the ~400 kB HTML pages.

You could say these [view states in ASP.NET](http://msdn.microsoft.com/en-us/library/ms972976.aspx) are similar to [sessions in PHP](http://www.php.net/manual/en/intro.session.php) (though they work differently under the hood), the dear developers working for the government showed their typical level of incompetence and fucked up really hard somehow managing to get the view state to contain data they should be pulling from their database plus some other data that is really long and looks like a solution to a really nasty server-side hack. What else they fucked up? Well, I'd encrypt session information. They didn't. [You can decode it](http://ignatu.co.uk/ViewStateDecoder.aspx), if you're the curious type. If you're the lazy and curious, here's what you'd find if you'd decode it: 1906 `bool` values set to `True`, 1906 `string` values counting from 1 to 18885 with steps of 10, then this, again, except this time the name of the first student are appended to the string (I'm guessing they use this to create the selection; because they obviously couldn't pull this from their database), then a bunch of strings and other objects, containing even more view state-irrelevant information, such as "Data ultimei actualizări: 16.07.2013 08:10".

Seriously, what the fuck? Was it written by 13-year-olds who know nothing about C# and ASP.NET? Anyways, I didn't expect much from an institution that [gives a text about Twilight](http://i.imgur.com/XNZfPgo.jpg) as the reading comprehension part of their English exam in 2013, and before that in 2012, on the listening part of the exam they spelled the name of [Jon Stewart as John Stuart](http://i.imgur.com/uB5cKYf.jpg). Clearly, even *THEY* failed the listening part. As a Daily Show watcher, that made me so furious, I couldn't take the rest of the English exam seriously. Still nailed it and got B2-B2-B2-B2-B2. 

#### parse13.php

Parses the pages located in `data13/` into three open formats: CSV, JSON and XML. It is recommended to run the whole thing on an SSD, otherwise it's going to take a while, since each student object is flushed to disk immediately after creation in order to avoid memory exhaustion issues.

This script will create three files in `YY-MM.FMT` format. Expect each file to be 45-130 MB in size.

This script *may* work with `13-09` data when it's published, given that the page layouts are not modified and the regular expressions still match.

## Miscellaneous stuff

#### wtf.js

This file was generated using:

    grep wtf 12-07.js > wtf.js
    grep wtf 12-09.js >> wtf.js
    grep wtf 13-07.js >> wtf.js

Upon writing the parsers, I thought to check the final grades against the grades the students got, as a funny way to unit-test the data. During the parser tests, where I only used a few random pages, the grade differences never went above the rounding error treshold, as expected.

However, upon parsing the complete database, I found that 15 grades differ from what they should be to the tune of up to `0.53`. On line 8 of `wtf.js` you will find a girl who failed with `5.58`, when her grade should be `6.11`, a passing grade.