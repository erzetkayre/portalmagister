<table width="100%">
    <tr>
        <td style="width: 20%; text-align: center;">
            <img src="{{ asset('media/logo-uns-biru.png') }}" style="height: 120px;"/>
        </td>
        <td style="width: 80%" align="center">
        <p style="font-size: 16px; margin:0; padding:0;"> KEMENTERIAN PENDIDIKAN DAN KEBUDAYAAN, <br/>RISET, DAN TEKNOLOGI<br/></p>
        <p style="font-size: 14px;">
            UNIVERSITAS SEBELAS MARET<br/>
            FAKULTAS TEKNIK <br/>
            <strong>PROGRAM STUDI TEKNIK ELEKTRO</strong>
            </p>
            <p style="margin:0; padding:0; font-size: 12px">Jalan Insinyur Sutami Nomor 36A Kentingan Surakarta 57126
            <br/> Telepon (0271) 647069, Faksimile (0271)662118
            <br/> Laman https://ft.uns.ac.id, Surel: teknik@ft.uns.ac.id</p>
        </td>
    </tr>
</table>

<?xml version="1.0" encoding="utf-8"?>
// XML Declaration - mendefinisikan versi XML dan encoding 

<LinearLayout 
    // Root Element - ViewGroup utama yang menampung semua child 
    xmlns:android="http://schemas.android.com/apk/res/android"
    // Namespace - mendefinisikan prefix 'android' untuk attributes 
    xmlns:app="http://schemas.android.com/apk/res-auto"
    // Custom namespace untuk attributes dari support libraries 
    xmlns:tools="http://schemas.android.com/tools"
    // Tools namespace - hanya untuk preview di Android Studio 
    
    android:layout_width="match_parent"
    // Attribute untuk width - match_parent berarti selebar parent 
    android:layout_height="match_parent"
    // Attribute untuk height 
    android:orientation="vertical"
    // Attribute spesifik LinearLayout 
    android:background="@color/white"
    // Resource reference ke color yang didefinisikan di colors.xml 
    
    tools:context=".MainActivity">
    // Tools attribute - memberitahu IDE context Activity ini 
    
    // Child Elements - Views atau ViewGroups lain 
    <TextView
        android:id="@+id/textViewHeader"
        // ID unik untuk reference dari Java code 
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        // wrap_content berarti setinggi content 
        android:text="@string/welcome_message"
        // String resource reference 
        android:textSize="@dimen/header_text_size"
        // Dimension resource reference 
        android:textColor="@color/primary_text"
        // Color resource reference 
        android:padding="@dimen/standard_padding"
        android:gravity="center"
        android:fontFamily="@font/roboto_bold" />
        // Font resource reference 
    
    <EditText
        android:id="@+id/editTextInput"
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        android:hint="@string/enter_your_name"
        android:inputType="textPersonName"
        android:layout_margin="@dimen/standard_margin" />
    
    <Button
        android:id="@+id/buttonSubmit"
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:text="@string/submit"
        android:layout_gravity="center_horizontal"
        android:background="@drawable/button_background"
        // Drawable resource reference 
        android:textColor="@android:color/white"
        // System color reference 
        style="@style/PrimaryButton" />
        // Style resource reference 
        
</LinearLayout>