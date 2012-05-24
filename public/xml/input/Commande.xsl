<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<!-- Import des règles de mise en forme -->
<xsl:import href="FormatFO.xsl"/>
<xsl:import href="Contenu.xsl"/>
<xsl:output encoding="utf-8"/>
<xsl:template match="PanierCommandes">
<xsl:call-template name="CONTENU"/>
</xsl:template>
</xsl:stylesheet>