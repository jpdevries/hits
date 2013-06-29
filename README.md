Hits
====

Counts page hits of MODX Resources and stores them in a custom table.

## Usage
Record a hit for resource 3.

    [[!Hits? &punch=`3`]]

Record 4 hit for resource 5.

    [[!Hits? &punch=`5` &amount=`4`]]

Get a comma seperated list of ids of the 10 most visited pages 10 levels down from the web context.

    [[!Hits? &parents=`0` &depth=`10` &limit=`10`]]

Get a comma seperated list of ids of the 4 least visited pages that are children of resource 2 and use your own hitInfo chunk to render results.

    [[!Hits? &parents=`2` limit=`4` &dir=`ASC`  &chunk=`hitInfo`]]

    
## Tips
Hits does two things:
 * allows you to record page hits based on a provided hit_key (such as a resource id)
 * optionally returns the results of a hit query
 
Hits can be used be used with getResources to list the most or least visited pages. This will pass a comma seperated list of ids of the 10 most visited pages according to Hits into getResources.

    [[getResources?
    &resources=`[[!Hits? &parents=`0` &depth=`10` &limit=`10` &outSeperator=`,`]]`
    ...
    ]]
    
## Optimization
Hits needs to be called uncached whenever it is recording hits using the punch paramter. If you don't want the recording of a hit to affect page load time at all you can use Hits with xFPC to record the hit after page load using AJAX.

When using with getResources to display listings of the most viewed pages remember that you can utilize getCache to cache the results to the filesystem for a determined period time as well as share the cache across multiple pages. If you are displaying a "Most Visited Pages" nav in your sidebar, the results are probably going to be the same across all or multiple pages. Thus, you can utilize the getCache cacheElementKey paramater to share the cache file across multiple (in this case all) resources. Put your getResources call in a Chunk named getMostViewed.

    [[!getCache?
    &element=`getMostViewed`
    &cacheExpires=`900`
    &cacheKey=`hits`
    &cacheElementKey=`getMostViewed`
    ]]
    
Our getMostViewed chunk will now only be processed every 15 minutes and load from a single shared cache. This means no matter how many visitors we have, we are only processing this output once every 15 minutes.

Alternatively, you could use wrap the Hits tag, rather than your entire getResources tag in getCache.

    [[getResources?
    &resources=`[[!getCache? &element=`mostHitsIDs` &cacheExpires=`900` &cacheKey=`hits` &cacheElementKey=`mostHitsIDs`]]`
    ...
    ]]




