Hits
====

Hits for MODX tracks pagehits with a punch by counting page hits of MODX Resources and storing them in a custom table.

Hits does two things:
 * allows you to record page hits based on a provided hit_key (such as a resource id)
 * Returns the results of a hit query


## Usage
Record a hit for resource 3.

    [[!Hits? &punch=`3`]]

Record 4 hit for resource 5.

    [[!Hits? &punch=`5` &amount=`4`]]

Get a comma seperated list of ids of the 10 most visited pages 10 levels down from the web context.

    [[!Hits? &parents=`0` &depth=`10` &limit=`10`]]

Get a comma seperated list of ids of the 4 least visited pages that are children of resource 2 and use your own hitInfo chunk to render results.

    [[!Hits? &parents=`2` limit=`4` &dir=`ASC`  &chunk=`hitInfo`]]


## Available Properties
| Name        | Description           | Default Value  | Added in version
| --------------|---------------| -----:| -----:|
| punch      | If set, a hit_key to record one or more hits for. Usually a resource id. |  |1.0.0
| amount      | The amount of hits to record for the punched hit_key.      |   1 |1.0.0
| parents | Comma-delimited list of ids serving as parents to search for most visited resources within. If provided, results are returned.      |     |1.0.0
| depth | Integer value indicating depth to search for resources from each parent. First level of resources beneath parent is depth.      |    10 |1.0.0
| tpl | Chunk to be used for formatting results.      |    hitTpl |1.0.0
| limit | The amount of results to return.      |    5 |1.0.0
| sort | Property to sort results on. Available options are hit_count, hit_key or id.      |    hit_count |1.0.0
| dir | Direction to sort properties on.      |    DESC |1.0.0
| outputSeperator | An optional string to separate each tpl instance.      |    "\n" |1.0.0
| toSeperator | If set, will assign the result to this placeholder instead of outputting it directly.      | |1.0.0

## With getResources
Hits can be used be used with getResources to list the most or least visited pages. This will pass a comma seperated list of ids of the 10 most visited pages according to Hits into getResources.

    [[getResources?
    &resources=`[[!Hits? &parents=`0` &depth=`10` &limit=`10` &outSeperator=`,`]]`
    ...
    ]]
    
## Optimization

#### Recording Hits
Hits needs to be called uncached whenever it is recording hits. If you don't want the recording of a hit to affect page load time you can use Hits with xFPC to record the hit after page load using AJAX.

#### Displaying Statistics
When using with getResources to display listings of the most viewed pages remember that you can utilize getCache to cache the results to the filesystem for a determined period time as well as share the cache across multiple pages. If you are displaying a "Most Visited Pages" nav in your sidebar, the results are probably going to be the same across all or multiple pages. Thus, you can utilize the getCache cacheElementKey paramater to share the cache file across multiple (in this case all) resources. Put your getResources call in a Chunk named getMostViewed.

    [[!getCache?
    &element=`getMostViewed`
    &cacheExpires=`900`
    &cacheKey=`hits`
    &cacheElementKey=`getMostViewed`
    ]]
    
The getMostViewed chunk will now only be processed every 15 minutes and load from a single shared cache. This means no matter how many visitors we have, we are only processing this output once every 15 minutes.

Alternatively, you could use wrap the Hits tag, rather than your entire getResources tag in getCache.

    [[getResources?
    &resources=`[[!getCache? &element=`mostHitsIDs` &cacheExpires=`900` &cacheKey=`hits` &cacheElementKey=`mostHitsIDs`]]`
    ...
    ]]




