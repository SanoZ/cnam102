<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format">
<xsl:template match="/">
<fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format">
<fo:layout-master-set>
<!-- Description de la page -->
<fo:simple-page-master master-name="page" page-height="29.7cm" page-width="21cm" margin-top="1cm" margin-bottom="1cm" margin-left="2.5cm" margin-right="2.5cm">
<!-- le corps de la page -->
<fo:region-body margin-top="4.1cm"/>
<!-- entête -->
<fo:region-before extent="4cm"/>
</fo:simple-page-master>
</fo:layout-master-set>
<!-- Déclenchement des règles -->
<xsl:apply-templates/>
</fo:root>
</xsl:template>
</xsl:stylesheet>