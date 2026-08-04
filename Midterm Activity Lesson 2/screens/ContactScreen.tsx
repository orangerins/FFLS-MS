import React from "react";
import { View, Text, StyleSheet } from "react-native";

export default function ContactScreen() {
  return (
    <View style={styles.container}>
      <Text style={styles.title}>Contact Information</Text>

      <View style={styles.content}>
        <Text style={styles.label}>Email</Text>
        <Text style={styles.text}>
          orangesuarez.mercado@my.smciligan.edu.ph
        </Text>

        <Text style={styles.label}>Phone Number</Text>
        <Text style={styles.text}>
          +63 975 477 6042
        </Text>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#EAF4FF",
    justifyContent: "center",
    alignItems: "center",
    padding: 20,
  },

  title: {
    fontSize: 28,
    fontWeight: "bold",
    color: "#1E3A8A",
    marginBottom: 25,
  },

  content: {
    backgroundColor: "#FFFFFF",
    width: "90%",
    padding: 20,
    borderRadius: 10,
  },

  label: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#1E3A8A",
    marginBottom: 5,
    marginTop: 10,
  },

  text: {
    fontSize: 16,
    color: "#333333",
    marginBottom: 10,
  },
});