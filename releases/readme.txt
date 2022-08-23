HISTORY
-------

Once upon a time...

There was a project, called Hero6 (http://www.hero6.com). The goal was to create
an unofficial sequal to the Quest for Glory series by Sierra On-Line.

During this time, we needed a good communications system, and we chose for a
forum. However, the only forums available at that time (most noticably the
UBB) were so expensive that we could never afford them.

I had recently gotten a book: Perl for Web Development (or something like
that), so I just went ahead and coded a forum. It was used quite actively
at Hero6, for many years, until I decided to leave the project to pursue
other careers.

It was around then, that I got active at the VirtualKid.com community, which
would later evolve to GovTeen.com. Either way, I made a lot of friends there
and people from Hero6 and GovTeen convinced me that I should sell the forum.

The forum was dubbed to ForuMAX, with an assorted domain name (ForuMAX.com,
which has now been hijacked!). It never really sold well, I think I sold
only about 30 copies or so; but still, it was very nice for that time (we
are talking begin 2000 here...) and I did learn a lot.

Later on, the ancient Perl version was really starting to grow old (mostly
because I am not a layout designer but a coder :), and PHP was starting to
be popular (PHP3 was recently released, I think). So, I undertook the massive
challenge of recoding the forum to PHP; this would become ForuMAX 5.

You will see a copyrights on 'The Internet Factory' or 'Next Future'; this
was a company I started with a friend in 2001 when we were both studying for
a bachelor in computer sciences. He (Emiel Roumen) did the layout for ForuMAX
5, which looks so much better than my crud in 4 :-)

ForuMAX 5 was never officially released, you can find the snapshot in this
folder. Installation is documented below.

Now, some nice things:

- ForuMAX/CGI (the 4.x version) was always used at Hero6.com prior to the
  rewrite (they use some hand-coded PHP forum now, I heard).  It did not need
  any databases, and has _NEVER_ suffered from data corruption, unlike the UBB.
- ForuMAX/PHP (the 5.x version) was used at GovTeen.com at one point, where
  it sustained a million hits per month without any problem. The server was
  a highend (for that time) Athlon XP 2000+. When I left GovTeen [1], the forum
  was downgraded to VBulletin.

I hope you will enjoy this, I now release them under the GPL so others can
play with it or borrow ideas.

FILES
-----

forumax4/
	forumax41.tgz		Last official release of ForuMAX
	mysql_module.tgz	Plugin module to store users in MySQL

forumax5/
 	fm5beta2.tgz		ForuMAX 5.0 BETA #2
 	fm5beta3.tgz		ForuMAX 5.0 BETA #3
	forumax5-current.tgz	ForuMAX 5.0, development sources
	forumax5-current.sql	ForuMAX 5.0, development database

extra/
	fm_lite.tgz		Lite version of ForuMAX, this was intended to
				be a free trial version. Never finished; I lost
				the sample database SQL...

INSTALLING FORUMAX 5
--------------------

I assume you want to install ForuMAX 5-CURRENT, as it is the most recent
version.

a) Create a MySQL database
b) Import forumax5-current.sql
c) Extract forumax5-current.tgz on your webserver
d) Edit dbconfig.php to reflect your database settings

You will have a fully-functional (well, almost!) forum; the control
panel is located in the 'cp' directory.

Administrator: username 'admin' password 'admin'
Master password: 'master'

INSTALLING FORUMAX 4
--------------------

Refer to the install.html instructions; these are quite clear

WARNING: If you accidently pass wrong information during the setup, your
Perl _WILL_ cause a deadlock! You can kill it and restart, but be very careful!

I never bothered to fix this ...

[1] This was more by force than my own decision, but that is another story.

THANKS
------

I would like to thank everyone who ordered their copy of ForuMAX back in the
day; all these persons have been emailed about this GPL-ed release.

Also a big thank you to Douglas Hazard (hosting of ForuMAX.com), Lee Benson
(Marketing assistance) and countless others who I forgot by name, but not in
my heart ...
