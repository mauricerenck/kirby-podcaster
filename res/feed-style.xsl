<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
    <xsl:template match="/">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <title><xsl:value-of select="/rss/channel/title"/> RSS Feed</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <style type="text/css">
                    body {
                        font-family: Arial, sans-serif;
                    }

                    .wrapper {
                        max-width: 800px;
                        margin: 0 auto;
                    }

                    .clearfix::after {
                        content: "";
                        clear: both;
                        display: table;
                    }

                    .abo-info {
                        margin: 20px;
                        padding: 20px;
                        border: 1px solid #8493a8;
                        border-radius: 3px;
                        background: #eceff4;
                    }

                    .channel {
                        border-bottom: 2px solid #ccc;
                        padding-bottom: 40px;
                    }

                    .channel header {
                        margin: 20px 0;
                    }

                    .channel .cover, .item .episode-image {
                        float: left;
                        width: 20%;
                        margin-right: 5%;
                    }

                    img {
                        width: 100%;
                    }

                    .channel header h1 {
                       font-size: 60px;
                    }

                    .channel header p {
                        float: left;
                        width: 60%;
                        line-height: 1.5;
                        margin-top: 0;
                    }

                    a {
                        color: #0082d5;
                        text-decoration: none;
                    }

                    .item {
                        margin: 20px 0 0 0;
                        padding: 20px 0;
                        border-bottom: 1px solid #ccc;
                    }

                    .download {
                        text-align: right;
                    }
                </style>
            </head>
            <body>
                <div class="wrapper">
                    <div class="channel clearfix">
                        <header>
                            <h1><xsl:value-of select="/rss/channel/title"/></h1>
                            <xsl:if test="/rss/channel/image">
                                <div class="cover">
                                    <img>
                                        <xsl:attribute name="src">
                                            <xsl:value-of select="/rss/channel/image/url"/>
                                        </xsl:attribute>
                                        <xsl:attribute name="title">
                                            <xsl:value-of select="/rss/channel/title"/>
                                        </xsl:attribute>
                                    </img>
                                </div>
                            </xsl:if>
                            <p><xsl:value-of select="/rss/channel/description"/></p>
                        </header>
                    </div>
                    <xsl:for-each select="/rss/channel/item">
                        <div class="item">
                            <h2>
                                <a>
                                    <xsl:attribute name="href">
                                        <xsl:value-of select="link"/>
                                    </xsl:attribute>
                                    <xsl:attribute name="target">_blank</xsl:attribute>
                                    <xsl:value-of select="title"/>
                                </a>
                            </h2>
                            <xsl:if test="description">
                                <p>
                                    <xsl:if test="image">
                                        <div class="episode-image">
                                            <img>
                                                <xsl:attribute name="src">
                                                    <xsl:value-of select="image/@href"/>
                                                </xsl:attribute>
                                                <xsl:attribute name="title">
                                                    <xsl:value-of select="title"/>
                                                </xsl:attribute>
                                                <xsl:attribute name="align">left</xsl:attribute>
                                            </img>
                                        </div>
                                    </xsl:if>
                                    <xsl:value-of select="description" disable-output-escaping="yes"/>
                                </p>
                            </xsl:if>
                            <p class="download">
                                <a>
                                    <xsl:attribute name="href">
                                        <xsl:value-of select="enclosure/@url"/>?ref=download
                                    </xsl:attribute>
                                    Download
                                </a> 
                                (<xsl:value-of select='format-number(number(enclosure/@length div "1024000"),"0.0")'/>MB)
                            </p>
                        </div>
                    </xsl:for-each>
                </div>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>