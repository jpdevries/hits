Hits tracks pagehits with a punch. Hits counts MODX Revolution page view and stores them in a custom table.

See project homepage at:
https://github.com/jpdevries/hits

USAGE: (assumes a chunk named hitID contains "[[+hit_key]]")
Get a comma seperated list of ids of the 10 most visited pages 10 levels down from the web context
[[!Hits? &parents=`0` &depth=`10` &limit=`10` &outSeperator=`,` &chunk=`hitID`]]

Get a comma seperated list of ids of the 4 least visited pages that are children of resource 2 and set results to a placeholder
[[!Hits? &parents=`2` limit=`4` &dir=`ASC`  &outSeperator=`,` &chunk=`hitID` &toPlaceholder=`hits`]]

Record a hit for resource 3
[[!Hits? &punch=`3`]]

Record 20 hit for resource 4
[[!Hits? &punch=`4` &amount=`20`]]

Remove 4 hit from resource 5
[[!Hits? &punch=`5` &amount=`-4`]]