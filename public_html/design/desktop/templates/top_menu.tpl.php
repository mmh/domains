<div class="header">
  <ul class="menu">
    <li class="active first"><a href="/servers/">Servers</a></li>
    <li><a href="/domains/">Domains</a></li>
    <li><a href="/accounts/">Accounts to Domains</a></li>
    <li><a href="/search/">Search</a></li>
    <li class="last"><a href="/cleanup/">Cleanup</a></li>
  </ul>
<br class="clr"/>
</div>
<br/>
<div id="fieldSelector">
  <form action="/service/ajax/serverList/json/" method="post" accept-charset="utf-8">
  <p>
    <label>Name:</label>
    <input type="checkbox" name="field[name]" value="1" />
    <label>IP:</label>
    <input type="checkbox" name="field[ip]" value="1" />
    <label>OS:</label>
    <input type="checkbox" name="field[os]" value="1" />
    <label>OS Release:</label>
    <input type="checkbox" name="field[os_release]" value="1" />
    <label>OS kernel:</label>
    <input type="checkbox" name="field[os_kernel]" value="1" />
    <label>Arch:</label>
    <input type="checkbox" name="field[arch]" value="1" />
    <label>Cpu Count:</label>
    <input type="checkbox" name="field[cpu_count]" value="1" />
    <label>Memory:</label>
    <input type="checkbox" name="field[memory]" value="1" />
    <label>Harddrives:</label>
    <input type="checkbox" name="field[harddrives]" value="1" />
    <label>Partitions:</label>
    <input type="checkbox" name="field[partitions]" value="1" />
    <label>Actions:</label>
    <input type="checkbox" name="field[actions]" value="1" />
    <label>Comment:</label>
    <input type="checkbox" name="field[comment]" value="1" />
    <input type="submit" value="Continue &rarr;" />
  </p>
  </form>
</div>
