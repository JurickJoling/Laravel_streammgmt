<icecast>
    <location>{{ $data->location }}</location>
    <admin>{{ $data->admin }}</admin>
    <limits>
        <clients>10000</clients>
        <sources>1000</sources>
        <threadpool>5</threadpool>
        <queue-size>524280</queue-size>
        <client-timeout>60</client-timeout>
        <header-timeout>40</header-timeout>
        <source-timeout>30</source-timeout>
        <!-- If enabled, this will provide a burst of data when a client
             first connects, thereby significantly reducing the startup
             time for listeners that do substantial buffering. However,
             it also significantly increases latency between the source
             client and listening client.  For low-latency setups, you
             might want to disable this. -->
        <burst-on-connect>1</burst-on-connect>
        <!-- same as burst-on-connect, but this allows for being more
             specific on how much to burst. Most people won't need to
             change from the default 64k. Applies to all mountpoints  -->
<!--        <burst-size>65535</burst-size> -->
    </limits>

    <authentication>
        <!-- Sources log in with username 'source' -->
        <source-password>{{ env('ICECAST_SOURCE_PASSWORD') }}</source-password>
        <!-- Relays log in username 'relay' -->
        <relay-password>{{ env('ICECAST_RELAY_PASSWORD') }}</relay-password>

        <!-- Admin logs in with the username given below -->
        <admin-user>admin</admin-user>
        <admin-password>{{ env('ICECAST_ADMIN_PASSWORD') }}</admin-password>
    </authentication>

    <!-- set the mountpoint for a shoutcast source to use, the default if not
         specified is /stream but you can change it here if an alternative is
         wanted or an extension is required
    <shoutcast-mount>/live.nsv</shoutcast-mount>
    -->

    <!-- Uncomment this if you want directory listings -->
    <!--
    <directory>
        <yp-url-timeout>15</yp-url-timeout>
        <yp-url>http://dir.xiph.org/cgi-bin/yp-cgi</yp-url>
    </directory>
     -->

    <!-- This is the hostname other people will use to connect to your server.
    It affects mainly the urls generated by Icecast for playlists and yp
    listings. -->
    <hostname>{{ env('ICECAST_HOSTNAME') }}</hostname>

    <!-- You may have multiple <listener> elements -->
    <listen-socket>
        <port>8000</port>
        <!-- <bind-address>127.0.0.1</bind-address> -->
        <!-- <shoutcast-mount>/stream</shoutcast-mount> -->
    </listen-socket>

    <listen-socket>
        <port>1035</port>
    </listen-socket>

    <!--<master-server>127.0.0.1</master-server>-->
    <!--<master-server-port>8001</master-server-port>-->
    <!--<master-update-interval>120</master-update-interval>-->
    <!--<master-password>hackme</master-password>-->

    <!-- setting this makes all relays on-demand unless overridden, this is
         useful for master relays which do not have <relay> definitions here.
         The default is 0 -->
    <!--<relays-on-demand>1</relays-on-demand>-->

    <!--
    <relay>
        <server>127.0.0.1</server>
        <port>8001</port>
        <mount>/example.ogg</mount>
        <local-mount>/different.ogg</local-mount>
        <on-demand>0</on-demand>
        <relay-shoutcast-metadata>0</relay-shoutcast-metadata>
    </relay>
    -->
    @foreach ($data->mounts as $mount)

@include('config.mount')

    @endforeach

    <!-- Fallback - Used for playing sound file -->
    <mount>
        <mount-name>/fallback</mount-name>
        <username>source</username>
        <password>Dkw4569!</password>
        <max-listeners>5000</max-listeners>
        <burst-size>65535</burst-size>
        <queue-size>65535</queue-size>
        <public>1</public>
      	<hidden>0</hidden>
        <no-yp>0</no-yp>
    </mount>

    <fileserve>1</fileserve>

    <paths>
		<!-- basedir is only used if chroot is enabled -->
        <basedir>/usr/local/share/icecast</basedir>

        <!-- Note that if <chroot> is turned on below, these paths must both
             be relative to the new root, not the original root -->
        <logdir>/var/log/icecast2</logdir>
        <webroot>/usr/local/share/icecast/web</webroot>
        <adminroot>/usr/local/share/icecast/admin</adminroot>
        <!-- <pidfile>/usr/share/icecast2/icecast.pid</pidfile> -->

        <!-- Aliases: treat requests for 'source' path as being for 'dest' path
             May be made specific to a port or bound address using the "port"
             and "bind-address" attributes.
          -->
@foreach($data->mounts as $mount)
@if ($mount->alias != '')
        <alias source="/{{ $mount->alias }}" dest="/{{ $mount->mount }}"/>
        <alias source="/{{ $mount->alias }}.mp3" dest="/{{ $mount->mount }}"/>
        <alias source="/{{ $mount->mount }}.mp3" dest="/{{ $mount->mount }}"/>
@endif
@endforeach
        <!--
        <alias source="/foo" dest="/bar"/>
          -->
        <!-- Aliases: can also be used for simple redirections as well,
             this example will redirect all requests for http://server:port/ to
             the status page
          -->
        <alias source="/" dest="/status.xsl"/>
    </paths>

    <logging>
        <accesslog>access.log</accesslog>
        <errorlog>error.log</errorlog>
        <!-- <playlistlog>playlist.log</playlistlog> -->
      	<loglevel>2</loglevel> <!-- 4 Debug, 3 Info, 2 Warn, 1 Error -->
      	<logsize>10000</logsize> <!-- Max size of a logfile -->
        <!-- If logarchive is enabled (1), then when logsize is reached
             the logfile will be moved to [error|access|playlist].log.DATESTAMP,
             otherwise it will be moved to [error|access|playlist].log.old.
             Default is non-archive mode (i.e. overwrite)
        -->
        <!-- <logarchive>1</logarchive> -->
    </logging>

    <security>
        <chroot>1</chroot>
        <changeowner>
            <user>icecast2</user>
            <group>icecast</group>
        </changeowner>
    </security>
</icecast>
