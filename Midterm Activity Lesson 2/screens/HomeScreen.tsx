import React from "react";
import { View, Text, StyleSheet, Button } from "react-native";
import { NativeStackNavigationProp } from "@react-navigation/native-stack";
import { RootStackParamList } from "../navigation/AppNavigator";

type HomeScreenProps = {
  navigation: NativeStackNavigationProp<RootStackParamList, "Home">;
};

export default function HomeScreen({ navigation }: HomeScreenProps) {
  return (
    <View style={styles.container}>
      <Text style={styles.title}>Student Portal</Text>

      <Text style={styles.subtitle}>
        Welcome! Select a page below to continue.
      </Text>

      <View style={styles.menuContainer}>
        <View style={styles.button}>
          <Button
            title="About"
            onPress={() => navigation.navigate("About")}
          />
        </View>

        <View style={styles.button}>
          <Button
            title="Contact"
            onPress={() => navigation.navigate("Contact")}
          />
        </View>

        <View style={styles.button}>
          <Button
            title="Profile"
            onPress={() => navigation.navigate("Profile")}
          />
        </View>
      </View>

      <Text style={styles.footer}>
        ©2026 All Rights Reserved
      </Text>
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
    fontSize: 32,
    fontWeight: "bold",
    color: "#1E3A8A",
    marginBottom: 10,
  },

  subtitle: {
    fontSize: 16,
    color: "#555555",
    textAlign: "center",
    marginBottom: 30,
  },

  menuContainer: {
    width: "90%",
    backgroundColor: "#FFFFFF",
    padding: 20,
    borderRadius: 10,
  },

  button: {
    marginBottom: 15,
  },

  footer: {
    marginTop: 30,
    fontSize: 14,
    color: "#666666",
    textAlign: "center",
  },
});