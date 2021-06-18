<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Trip Builder API</title>

    <link href="https://fonts.googleapis.com/css?family=PT+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("vendor/scribe/css/theme-default.print.css") }}" media="print">
    <script src="{{ asset("vendor/scribe/js/theme-default-3.1.0.js") }}"></script>

    <link rel="stylesheet"
          href="//unpkg.com/@highlightjs/cdn-assets@10.7.2/styles/obsidian.min.css">
    <script src="//unpkg.com/@highlightjs/cdn-assets@10.7.2/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>

    <script src="//cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>
    <script>
        var baseUrl = "http://localhost:8007";
    </script>
    <script src="{{ asset("vendor/scribe/js/tryitout-3.1.0.js") }}"></script>

</head>

<body class="" data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">
<a href="#" id="nav-button">
      <span>
        MENU
        <img src="{{ asset("vendor/scribe/images/navbar.png") }}" alt="navbar-image" />
      </span>
</a>
<div class="tocify-wrapper">
                <div class="lang-selector">
                            <a href="#" data-language-name="bash">bash</a>
                            <a href="#" data-language-name="javascript">javascript</a>
                    </div>
        <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>
    <ul class="search-results"></ul>

    <ul id="toc">
    </ul>

            <ul class="toc-footer" id="toc-footer">
                            <li><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                    </ul>
            <ul class="toc-footer" id="last-updated">
            <li>Last updated: June 20 2021</li>
        </ul>
</div>
<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1>Introduction</h1>
<p>This documentation aims to provide all the information you need to work with our API.</p>
<blockquote>
<p>Base URL</p>
</blockquote>
<pre><code class="language-yaml">http://localhost:8007</code></pre>

        <h1>Authenticating requests</h1>
<p>This API is not authenticated.</p>

        <h1 id="endpoints">Endpoints</h1>
    <p>
        
    </p>

            <h2 id="endpoints-POSTapi-flight-search">api/flight/search</h2>

<p>
</p>



<blockquote>Example request:</blockquote>


<pre><code class="language-bash">curl --request POST \
    "http://localhost:8007/api/flight/search" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{\"departure_location\":\"YHZ\",\"destination_location\":\"YVR\",\"departure_date\":\"2021-07-01\",\"return_date\":\"2021-07-10\",\"restrict_airlines\":\"AC,F8\",\"page_size\":20,\"page_number\":1,\"sort_by\":\"duration\",\"maxmum_stops\":2,\"keep_going_forward\":true}"</code></pre>

<pre><code class="language-javascript">const url = new URL(
    "http://localhost:8007/api/flight/search"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "departure_location": "YHZ",
    "destination_location": "YVR",
    "departure_date": "2021-07-01",
    "return_date": "2021-07-10",
    "restrict_airlines": "AC,F8",
    "page_size": 20,
    "page_number": 1,
    "sort_by": "duration",
    "maxmum_stops": 2,
    "keep_going_forward": true
}

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre>

<div id="execution-results-POSTapi-flight-search" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-flight-search"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-flight-search"></code></pre>
</div>
<div id="execution-error-POSTapi-flight-search" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-flight-search"></code></pre>
</div>
<form id="form-POSTapi-flight-search" data-method="POST"
      data-path="api/flight/search"
      data-authed="0"
      data-hasfiles="0"
      data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}'
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-flight-search', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-flight-search"
                    onclick="tryItOut('POSTapi-flight-search');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-flight-search"
                    onclick="cancelTryOut('POSTapi-flight-search');" hidden>Cancel
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-flight-search" hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/flight/search</code></b>
        </p>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <p>
            <b><code>departure_location</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="departure_location" data-endpoint="POSTapi-flight-search" data-component="body" required  hidden>
<br>
<p>an airport or city code identified by IATA.</p>        </p>
                <p>
            <b><code>destination_location</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="destination_location" data-endpoint="POSTapi-flight-search" data-component="body" required  hidden>
<br>
<p>an airport or city code identified by IATA.</p>        </p>
                <p>
            <b><code>departure_date</code></b>&nbsp;&nbsp;<small>date</small>  &nbsp;
<input type="text" name="departure_date" data-endpoint="POSTapi-flight-search" data-component="body" required  hidden>
<br>
<p>Must be a valid date. Must be a date after now.</p>        </p>
                <p>
            <b><code>return_date</code></b>&nbsp;&nbsp;<small>date</small>     <i>optional</i> &nbsp;
<input type="text" name="return_date" data-endpoint="POSTapi-flight-search" data-component="body"  hidden>
<br>
<p>Must be a valid date. Must be a date after or equal to departure_date.</p>        </p>
                <p>
            <b><code>restrict_airlines</code></b>&nbsp;&nbsp;<small>string</small>     <i>optional</i> &nbsp;
<input type="text" name="restrict_airlines" data-endpoint="POSTapi-flight-search" data-component="body"  hidden>
<br>
<p>restrict to preferrd airlines.</p>        </p>
                <p>
            <b><code>page_size</code></b>&nbsp;&nbsp;<small>number</small>     <i>optional</i> &nbsp;
<input type="number" name="page_size" data-endpoint="POSTapi-flight-search" data-component="body"  hidden>
<br>
<p>Must be at least 1, default value 20.</p>        </p>
                <p>
            <b><code>page_number</code></b>&nbsp;&nbsp;<small>number</small>     <i>optional</i> &nbsp;
<input type="number" name="page_number" data-endpoint="POSTapi-flight-search" data-component="body"  hidden>
<br>
<p>Must be at least 1, default value 1.</p>        </p>
                <p>
            <b><code>sort_by</code></b>&nbsp;&nbsp;<small>enum</small>     <i>optional</i> &nbsp;
<input type="text" name="sort_by" data-endpoint="POSTapi-flight-search" data-component="body"  hidden>
<br>
<p>Must be one of duration, price, or stops, default value: duration.</p>        </p>
                <p>
            <b><code>maxmum_stops</code></b>&nbsp;&nbsp;<small>number</small>     <i>optional</i> &nbsp;
<input type="number" name="maxmum_stops" data-endpoint="POSTapi-flight-search" data-component="body"  hidden>
<br>
<p>Must be between 0 and 5, default value 2.</p>        </p>
                <p>
            <b><code>keep_going_forward</code></b>&nbsp;&nbsp;<small>boolean</small>     <i>optional</i> &nbsp;
<label data-endpoint="POSTapi-flight-search" hidden><input type="radio" name="keep_going_forward" value="true" data-endpoint="POSTapi-flight-search" data-component="body" ><code>true</code></label>
<label data-endpoint="POSTapi-flight-search" hidden><input type="radio" name="keep_going_forward" value="false" data-endpoint="POSTapi-flight-search" data-component="body" ><code>false</code></label>
<br>
<p>if true, ever further flight should get closer to the destination, otherwise may not, default value true.</p>        </p>
    
    </form>

    

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                    <a href="#" data-language-name="bash">bash</a>
                                    <a href="#" data-language-name="javascript">javascript</a>
                            </div>
            </div>
</div>
<script>
    $(function () {
        var exampleLanguages = ["bash","javascript"];
        setupLanguages(exampleLanguages);
    });
</script>
</body>
</html>