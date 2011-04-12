<div id="search" class="page">
  <form action="#" method="post" accept-charset="utf-8" id="search_form">

    <div class="help">Search after stuff</div>

    <div class="ui-widget">
      <label for="searchQuery">Search query:</label>
      <input type="text" name="searchQuery" value="" id="searchQuery"/>
      <label for="type">Type:</label>
      <input type="radio" name="type" value="server" checked="checked"/> Server
      <input type="radio" name="type" value="domain" /> Domain
      <input type="radio" name="type" value="owner" /> Owner
      <input type="checkbox" name="wildcards" value="1" id="wildcards" /> Wildcards on both sides of query
    </div>
  </form>
  <br/>
  <table id="result">
    <thead>
      <th class="status">Status</th>
      <th class="name">Name</th>
      <th class="desc">Desc</th>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>
