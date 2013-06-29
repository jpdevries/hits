hits
====

Counts page hits of MODX Resources

### USAGE:
Get a comma seperated list of ids of the 10 most visited pages 10 levels down from the web context
[[!Hits? &parents=`0` &depth=`10` &limit=`10` &outSeperator=`,` &chunk=`hitID`]]

Get a comma seperated list of ids of the 4 least visited pages that are children of resource 2
[[!Hits? &parents=`2` limit=`4` &dir=`ASC`  &outSeperator=`,` &chunk=`hitID`]]

Record a hit for resource 3
[[!Hits? &punch=`3`]]

Record 4 hit for resource 5
[[!Hits? &punch=`5` &amount=`4`]]
