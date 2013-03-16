<?
header('Content-type: application/xml');
echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/"
                       xmlns:moz="http://www.mozilla.org/2006/browser/search/">
<ShortName>OBST</ShortName>
<Description>DS-OBST</Description>
<InputEncoding>inputEncoding</InputEncoding>
<Image width="16" height="16" type="image/x-icon">data:image/x-icon;base64,AAABAAEAEBAAAAEAGABoAwAAFgAAACgAAAAQAAAAIAAAAAEAGAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABKSFNdYW0AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABbgI9cgI07OERER1Reg5Jcg5IAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABWfo56n6+PqbOIoKwLSmMxY3qEm6aZs712m6lWfo4AAAAAAAAAAAAAAAAAAABSeYqZvsifwc+OqLJvg48lUmdCbIBzipeXs72iw8+YvchPd4sAAAAAAAAAAABVe4qXvMucw9Kjx9SgxNWjxNQzTmJpipijxtSgx9WIorCXvMeXvctZg5QAAAAAAABskqKfw9Kfwc+ZwM+lxtGbwM8vTF9jhZifxM+lxM9AT1KNprKZwM9vl6YAAABWfY2mxs6lxtGpx9GjxM+lxNGmxs8RVW9Aeo6jwc4nMzY4RUh0io+mxM+jx9JUeopSeoqlyNKgxs+lytKgxs+jyNKjyNJBWV8zR0wRHB8AAAAnMDCjyNGew9GcwdFMcYFPdoSewcuewc2cvsqewMuewcuYucQHEhUAAAAAAAAAAABqgIWewcucwMqewctLcH1SeYejxtSjx9GmytSlx9SmytQjMTQAAAAAAAAAAAAgIB+lxtGlyNKjx9SjyNVRd4VbgZKfxM+ox9Kmx9KmxtKHoqkAAAAAAAAAAAAAAACXsLepxtGmx9SjxNGgxM9WfY0AAABxkqCpxM6qxs+qxM87SEwAAAAAAAAAAABpdnqsx9Gqw8+qxs+qx89tjpwAAAAAAABehJKPtMCgw82lw81LX2MAAAAAAAATHByjwcugw86mw86ixM6RtMBcg5EAAAAAAAAAAABZgI+XtMCyztWtx88IDxEAAAAAAABWYmKmvsayzdWXtsBVeooAAAAAAAAAAAAAAAAAAABji5lih5WZvsqcvspUZmpqg4iYtsCbwMtmjZxiiJgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABfhJJYfo1bgI9VeohZgI5jipsAAAAAAAAAAAAAAAAAAAD+fwAA+B8AAOAHAADAAwAAgAEAAIABAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIABAACAAQAAwAMAAOAHAAD4HwAA</Image>
<Url type="text/html" method="GET" template="http://<?=$_SERVER['HTTP_HOST']?>/obst/obst-ab/index.php">
  <Param name="coord" value="{searchTerms}"/>
  <Param name="page" value="reports"/>
  <Param name="action" value="searchex"/>
</Url>
<moz:SearchForm>http://<?=$_SERVER['HTTP_HOST']?>/obst/obst-ab/index.php</moz:SearchForm>
</OpenSearchDescription>
